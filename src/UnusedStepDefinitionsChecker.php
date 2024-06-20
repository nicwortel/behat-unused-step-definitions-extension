<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension;

use Behat\Behat\Definition\Definition;
use Behat\Behat\Definition\DefinitionFinder;
use Behat\Behat\Definition\DefinitionRepository;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function array_diff;
use function array_filter;
use function preg_match;
use function sprintf;

final class UnusedStepDefinitionsChecker implements EventSubscriberInterface
{
    /**
     * @var array<Definition>
     */
    private array $usedDefinitions = [];

    /**
     * @param array{include: string[], exclude: string[]}|null $filters
     */
    public function __construct(
        private readonly DefinitionFinder $definitionFinder,
        private readonly DefinitionRepository $definitionRepository,
        private readonly UnusedStepDefinitionsPrinter $printer,
        private readonly bool $ignorePatternAliases,
        private readonly ?array $filters,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterStepTested::AFTER => 'addUsedStepDefinition',
            AfterSuiteTested::AFTER => 'checkUnusedStepDefinitions',
        ];
    }

    public function addUsedStepDefinition(AfterStepTested $event): void
    {
        $definition = $this->definitionFinder->findDefinition(
            $event->getEnvironment(),
            $event->getFeature(),
            $event->getStep()
        )->getMatchedDefinition();

        if ($definition === null) {
            return;
        }

        $this->usedDefinitions[] = $definition;
    }

    public function checkUnusedStepDefinitions(AfterSuiteTested $event): void
    {
        $definitions = $this->definitionRepository->getEnvironmentDefinitions($event->getEnvironment());

        /** @var Definition[] $unusedDefinitions */
        $unusedDefinitions = array_diff($definitions, $this->usedDefinitions);

        if ($this->ignorePatternAliases) {
            $unusedDefinitions = $this->filterPatternAliases($unusedDefinitions);
        }

        if ($this->filters) {
            $unusedDefinitions = $this->filterIncludeExclude($unusedDefinitions);
        }

        $this->printer->printUnusedStepDefinitions($unusedDefinitions);
    }

    /**
     * @param Definition[] $unusedDefinitions
     * @return Definition[]
     */
    private function filterPatternAliases(array $unusedDefinitions): array
    {
        $usedPaths = [];
        foreach ($this->usedDefinitions as $def) {
            $usedPaths[$def->getPath()] = true;
        }

        return array_filter($unusedDefinitions, function (Definition $definition) use ($usedPaths) {
            return !isset($usedPaths[$definition->getPath()]);
        });
    }

    /**
     * @param Definition[] $unusedDefinitions
     * @return Definition[]
     */
    private function filterIncludeExclude(array $unusedDefinitions): array
    {
        return array_filter($unusedDefinitions, function (Definition $definition) {
            // Get the concrete path reference for this definition.
            $path = $this->getConcretePath($definition->getReflection());

            $includePatterns = $this->filters['include'] ?? null;
            $includeMatch = $includePatterns ? $this->matchesPatterns($path, $includePatterns) : true;

            $excludePatterns = $this->filters['exclude'] ?? null;
            $excludeMatch = $excludePatterns ? $this->matchesPatterns($path, $excludePatterns) : false;

            return $includeMatch && !$excludeMatch;
        });
    }

    /**
     * @param string[] $patterns
     */
    private function matchesPatterns(string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ((bool) preg_match($pattern, $path)) {
                return true;
            }
        }
        return false;
    }

    private function getConcretePath(ReflectionFunctionAbstract $function): string
    {
        return $function instanceof ReflectionMethod
            ? sprintf('%s::%s()', $function->getDeclaringClass()->getName(), $function->getName())
            : sprintf('%s()', $function->getName());
    }
}

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
use function implode;
use function preg_match;
use function sprintf;

final class UnusedStepDefinitionsChecker implements EventSubscriberInterface
{
    /**
     * @var array<Definition>
     */
    private array $usedDefinitions = [];

    public function __construct(
        private readonly DefinitionFinder $definitionFinder,
        private readonly DefinitionRepository $definitionRepository,
        private readonly UnusedStepDefinitionsPrinter $printer,
        private readonly ?array $filters
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

        if ($this->filters) {
            $unusedDefinitions = array_filter($unusedDefinitions, [$this, 'matchFilters']);
        }

        $this->printer->printUnusedStepDefinitions($unusedDefinitions);
    }

    private function matchFilters(Definition $definition): bool
    {
        // Get the concrete path reference for this definition.
        $path = $this->getConcretePath($definition->getReflection());

        $include = $this->filters['include'] ?? null;
        $match_include = !empty($include) ? $this->patternsMatch($path, $include) : true;

        $exclude = $this->filters['exclude'] ?? null;
        $match_exclude = !empty($exclude) ? $this->patternsMatch($path, $exclude) : false;

        return $match_include && !$match_exclude;
    }

    /**
     * @param string[] $patterns
     */
    private function patternsMatch(string $path, array $patterns): bool
    {
        $pattern = '/' . implode('|', $patterns) . '/';
        return (bool) preg_match($pattern, $path);
    }

    private function getConcretePath(ReflectionFunctionAbstract $function): string
    {
        return $function instanceof ReflectionMethod
            ? sprintf('%s::%s()', $function->getDeclaringClass()->getName(), $function->getName())
            : sprintf('%s()', $function->getName());
    }
}

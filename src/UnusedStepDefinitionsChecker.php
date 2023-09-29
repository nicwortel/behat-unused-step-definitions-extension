<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension;

use Behat\Behat\Definition\Definition;
use Behat\Behat\Definition\DefinitionFinder;
use Behat\Behat\Definition\DefinitionRepository;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function array_diff;
use function array_filter;
use function preg_match;

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
        private readonly ?string $filter
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

        if ($this->filter) {
            $unusedDefinitions = array_filter(
                $unusedDefinitions,
                fn(Definition $definition): bool => (bool) preg_match((string) $this->filter, $definition->getPath())
            );
        }

        $this->printer->printUnusedStepDefinitions($unusedDefinitions);
    }
}

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

final class UnusedStepDefinitionsChecker implements EventSubscriberInterface
{
    private DefinitionFinder $definitionFinder;

    private DefinitionRepository $definitionRepository;

    private UnusedStepDefinitionsPrinter $printer;

    private ?string $filter;

    /**
     * @var array<Definition>
     */
    private array $usedDefinitions = [];

    public function __construct(
        DefinitionFinder $definitionFinder,
        DefinitionRepository $definitionRepository,
        UnusedStepDefinitionsPrinter $printer,
        ?string $filter
    ) {
        $this->definitionFinder = $definitionFinder;
        $this->definitionRepository = $definitionRepository;
        $this->printer = $printer;
        $this->filter = $filter;
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
            $unusedDefinitions = array_filter($unusedDefinitions, function (Definition $definition): bool
            {
               return (bool) preg_match($this->filter, $definition->getPath());
            });
        }

      $this->printer->printUnusedStepDefinitions($unusedDefinitions);
    }
}

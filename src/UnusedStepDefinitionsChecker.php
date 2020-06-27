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
    /**
     * @var DefinitionFinder
     */
    private $definitionFinder;

    /**
     * @var DefinitionRepository
     */
    private $definitionRepository;

    /**
     * @var UnusedStepDefinitionsPrinter
     */
    private $printer;

    /**
     * @var array<Definition>
     */
    private $usedDefinitions = [];

    public function __construct(
        DefinitionFinder $definitionFinder,
        DefinitionRepository $definitionRepository,
        UnusedStepDefinitionsPrinter $printer
    ) {
        $this->definitionFinder = $definitionFinder;
        $this->definitionRepository = $definitionRepository;
        $this->printer = $printer;
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

        $this->printer->printUnusedStepDefinitions($unusedDefinitions);
    }
}

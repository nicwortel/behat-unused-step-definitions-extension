<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension;

use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final class ConsoleUnusedStepDefinitionsPrinter implements UnusedStepDefinitionsPrinter
{
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function printUnusedStepDefinitions(array $unusedDefinitions): void
    {
        foreach ($unusedDefinitions as $unusedDefinition) {
            $this->output->writeln(sprintf(
                'Unused definition: %s %s',
                $unusedDefinition->getType(),
                $unusedDefinition->getPattern()
            ));
        }
    }
}

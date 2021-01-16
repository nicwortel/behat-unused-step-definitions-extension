<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension;

use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function sprintf;

final class ConsoleUnusedStepDefinitionsPrinter implements UnusedStepDefinitionsPrinter
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function printUnusedStepDefinitions(array $unusedDefinitions): void
    {
        if (count($unusedDefinitions) === 0) {
            return;
        }

        $this->output->writeln(
            sprintf('<comment>%d unused step definitions</comment>:', count($unusedDefinitions))
        );

        foreach ($unusedDefinitions as $unusedDefinition) {
            $this->output->writeln(
                sprintf(
                    ' - <comment>%s %s</comment> <def_dimmed># %s</def_dimmed>',
                    $unusedDefinition->getType(),
                    $unusedDefinition->getPattern(),
                    $unusedDefinition->getPath()
                )
            );
        }

        $this->output->writeln('');
    }
}

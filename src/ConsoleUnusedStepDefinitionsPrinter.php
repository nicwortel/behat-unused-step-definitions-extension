<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension;

use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function sprintf;

final class ConsoleUnusedStepDefinitionsPrinter implements UnusedStepDefinitionsPrinter
{
    public function __construct(private readonly OutputInterface $output)
    {
    }

    /**
     * @inheritDoc
     */
    public function printUnusedStepDefinitions(array $unusedDefinitions): void
    {
        if ($unusedDefinitions === []) {
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

<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension\Tests;

use NicWortel\BehatUnusedStepDefinitionsExtension\UnusedStepDefinitionsPrinter;

use function file_put_contents;
use function sprintf;
use function sys_get_temp_dir;

use const FILE_APPEND;

class TestingPrinter implements UnusedStepDefinitionsPrinter
{
    /**
     * @inheritDoc
     */
    public function printUnusedStepDefinitions(array $unusedDefinitions): void
    {
        $outputFile = sys_get_temp_dir() . '/unused_step_defs.txt';
        foreach ($unusedDefinitions as $unusedDefinition) {
            file_put_contents(
                $outputFile,
                sprintf(
                    "%s %s # %s\n",
                    $unusedDefinition->getType(),
                    $unusedDefinition->getPattern(),
                    $unusedDefinition->getPath()
                ),
                FILE_APPEND
            );
        }
    }
}

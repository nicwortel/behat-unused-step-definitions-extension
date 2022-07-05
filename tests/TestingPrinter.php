<?php

namespace NicWortel\BehatUnusedStepDefinitionsExtension\Tests;

use NicWortel\BehatUnusedStepDefinitionsExtension\UnusedStepDefinitionsPrinter;

class TestingPrinter implements  UnusedStepDefinitionsPrinter
{
    public function printUnusedStepDefinitions(array $unusedDefinitions): void {
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

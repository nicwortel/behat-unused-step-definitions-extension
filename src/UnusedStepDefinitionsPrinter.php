<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension;

use Behat\Behat\Definition\Definition;

interface UnusedStepDefinitionsPrinter
{
    /**
     * @param Definition[] $unusedDefinitions
     */
    public function printUnusedStepDefinitions(array $unusedDefinitions): void;
}

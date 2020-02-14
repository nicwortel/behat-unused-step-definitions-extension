<?php
declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class BehatExtensionTest extends TestCase
{
    public function testPrintsUnusedStepDefinitions(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--config', 'behat.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $this->assertStringContainsString('Unused definition: Given some precondition that is never used in a feature', $behat->getOutput());
        $this->assertStringContainsString('Unused definition: Then some step that is never used by a feature', $behat->getOutput());
    }

    public function testDoesNotPrintStepDefinitionsThatAreUsed(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--config', 'behat.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $this->assertStringNotContainsString('Unused definition: When some action by the actor', $behat->getOutput());
    }
}

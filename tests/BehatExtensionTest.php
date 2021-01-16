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

        $this->assertStringContainsString('2 unused step definitions:', $behat->getOutput());

        $this->assertStringContainsString(
            'Given some precondition that is never used in a feature # FeatureContext::somePrecondition()',
            $behat->getOutput()
        );
        $this->assertStringContainsString(
            'Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()',
            $behat->getOutput()
        );
    }

    public function testDoesNotPrintStepDefinitionsThatAreUsed(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--config', 'behat.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $this->assertStringNotContainsString(
            'When some action by the actor # FeatureContext::someActionByTheActor()',
            $behat->getOutput()
        );
    }
}

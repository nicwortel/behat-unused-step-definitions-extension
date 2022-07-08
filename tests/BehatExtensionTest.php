<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

use function file_get_contents;
use function sys_get_temp_dir;

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

    public function testCustomPrinter(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--config', 'behat_extended.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $outputFile = sys_get_temp_dir() . '/unused_step_defs.txt';
        $this->assertFileExists($outputFile);
        $fileContents = file_get_contents($outputFile);
        $this->assertStringContainsString(
            'Given some precondition that is never used in a feature # FeatureContext::somePrecondition()',
            $fileContents
        );
        $this->assertStringContainsString(
            'Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()',
            $fileContents
        );
        $this->assertStringNotContainsString(
            'When some action by the actor # FeatureContext::someActionByTheActor()',
            $fileContents
        );
    }

    public function testWithFilter(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--config', 'behat_filtered.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $this->assertStringContainsString('1 unused step definitions:', $behat->getOutput());

        $this->assertStringNotContainsString(
            'Given some precondition that is never used in a feature # FeatureContext::somePrecondition()',
            $behat->getOutput()
        );
        $this->assertStringNotContainsString(
            'When some action by the actor # FeatureContext::someActionByTheActor()',
            $behat->getOutput()
        );
        // Only this step definition is passing the filter.
        $this->assertStringContainsString(
            'Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()',
            $behat->getOutput()
        );
    }
}

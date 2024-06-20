<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

use function file_get_contents;
use function sys_get_temp_dir;
use function unlink;

use const PHP_EOL;

class BehatExtensionTest extends TestCase
{
    public function testPrintsUnusedStepDefinitions(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'default', '--config', 'behat.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $expected = <<<EOF
3 unused step definitions:
 - Given some precondition that is never used in a feature # FeatureContext::somePrecondition()
 - Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()
 - Then some step defined in the base class that is never used by a feature # FeatureContext::someBaseClassStepThatIsNeverUsedByAFeature()
EOF;
        $this->assertStringContainsString($expected, $behat->getOutput());
    }

    public function testIgnorePatternAliases(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'ignore_pattern_aliases', '--config', 'behat.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $expected = <<<EOF
2 unused step definitions:
 - Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()
 - Then some step defined in the base class that is never used by a feature # FeatureContext::someBaseClassStepThatIsNeverUsedByAFeature()
EOF;
        $this->assertStringContainsString($expected, $behat->getOutput());
    }

    public function testCustomPrinter(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'custom_printer', '--config', 'behat.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $outputFile = sys_get_temp_dir() . '/unused_step_defs.txt';
        $this->assertFileExists($outputFile);
        $fileContents = file_get_contents($outputFile);
        unlink($outputFile);

        $expected = <<<EOF
Given some precondition that is never used in a feature # FeatureContext::somePrecondition()
Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()
Then some step defined in the base class that is never used by a feature # FeatureContext::someBaseClassStepThatIsNeverUsedByAFeature()
EOF;
        $this->assertEquals($expected . PHP_EOL, $fileContents);
    }

    public function testFilterInclude(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'include', '--config', 'behat_filtered.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $expected = <<<EOF
1 unused step definitions:
 - Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()
EOF;
        $this->assertStringContainsString($expected, $behat->getOutput());
    }

    public function testFilterIncludeBC(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'include_bc', '--config', 'behat_filtered.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $expected = <<<EOF
1 unused step definitions:
 - Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()
EOF;
        $this->assertStringContainsString($expected, $behat->getOutput());
    }

    public function testFilterIncludeInheritance(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'include_inheritance', '--config', 'behat_filtered.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $expected = <<<EOF
2 unused step definitions:
 - Given some precondition that is never used in a feature # FeatureContext::somePrecondition()
 - Then some step that is never used by a feature # FeatureContext::someStepThatIsNeverUsedByAFeature()
EOF;
        $this->assertStringContainsString($expected, $behat->getOutput());
    }

    public function testFilterExclude(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'exclude', '--config', 'behat_filtered.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $expected = <<<EOF
2 unused step definitions:
 - Given some precondition that is never used in a feature # FeatureContext::somePrecondition()
 - Then some step defined in the base class that is never used by a feature # FeatureContext::someBaseClassStepThatIsNeverUsedByAFeature()
EOF;
        $this->assertStringContainsString($expected, $behat->getOutput());
    }

    public function testFilterIncludeExclude(): void
    {
        $behat = new Process(['../../vendor/bin/behat', '--profile', 'include_exclude', '--config', 'behat_filtered.yml'], __DIR__ . '/fixtures/');
        $behat->mustRun();

        $expected = <<<EOF
1 unused step definitions:
 - Given some precondition that is never used in a feature # FeatureContext::somePrecondition()
EOF;
        $this->assertStringContainsString($expected, $behat->getOutput());
    }
}

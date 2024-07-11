<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

use function file_get_contents;
use function sys_get_temp_dir;
use function unlink;

class BehatExtensionTest extends TestCase
{
    /**
     * @return array<array{0: string}>
     */
    public static function behatProfilesProvider(): array
    {
        return [
          ['default'],
          ['ignore_pattern_aliases'],
          ['filter_bc'],
          ['filter_inheritance'],
          ['filter_include'],
          ['filter_exclude'],
          ['filter_include_exclude'],
        ];
    }

    /**
     * @dataProvider behatProfilesProvider
     */
    public function testBehatProfiles(string $profile): void
    {
        $actual = $this->runBehat($profile);
        $expected = file_get_contents(__DIR__ . "/fixtures/expectations/$profile.txt");
        $this->assertStringContainsString($expected, $actual);
    }

    public function testCustomPrinter(): void
    {
        $profile = 'custom_printer';
        $outputFile = sys_get_temp_dir() . '/unused_step_defs.txt';
        $this->runBehat($profile);

        $this->assertFileExists($outputFile);
        $actual = file_get_contents($outputFile);
        unlink($outputFile);

        $expected = file_get_contents(__DIR__ . "/fixtures/expectations/$profile.txt");
        $this->assertEquals($expected, $actual);
    }

    private function runBehat(string $profile): string
    {
          $behat = new Process(['../../vendor/bin/behat', '--profile', $profile], __DIR__ . '/fixtures/');
          return $behat->mustRun()->getOutput();
    }
}

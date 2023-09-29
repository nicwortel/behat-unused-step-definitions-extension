<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->importNames();

    // register a single rule
    // $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SetList::CODE_QUALITY,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_100,
    ]);
};

<?php

namespace NicWortel\BehatUnusedStepDefinitionsExtension\Tests;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TestingBehatExtension implements Extension
{
    public function load(ContainerBuilder $container, array $config): void {
        $container->setDefinition(
            'testing_printer',
            new Definition(TestingPrinter::class)
        );
    }

    public function getConfigKey(): string {
        return 'testing_behat_extension';
    }

    public function process(ContainerBuilder $container): void {}
    public function initialize(ExtensionManager $extensionManager): void {}
    public function configure(ArrayNodeDefinition $builder): void {}
}

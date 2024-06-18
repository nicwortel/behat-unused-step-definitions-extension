<?php

declare(strict_types=1);

namespace NicWortel\BehatUnusedStepDefinitionsExtension;

use Behat\Behat\Definition\ServiceContainer\DefinitionExtension;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\ServiceContainer\Extension as BehatExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class Extension implements BehatExtension
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'unused_step_definitions';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder->children()
            ->scalarNode('printer')
                ->defaultValue('unused_step_definitions_printer')
            ->end()
            ->booleanNode('ignorePatternAliases')
                ->defaultFalse()
            ->end()
            ->arrayNode('filters')
                ->info('Specifies include/exclude filters')
                ->performNoDeepMerging()
                ->children()
                    ->arrayNode('include')
                        ->defaultValue([])
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                    ->arrayNode('exclude')
                        ->defaultValue([])
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * @param array{printer: string, filter: ?string} $config
     */
    public function load(ContainerBuilder $container, array $config): void
    {
        $serviceDefinition = new Definition(
            UnusedStepDefinitionsChecker::class,
            [
                new Reference(DefinitionExtension::FINDER_ID),
                new Reference(DefinitionExtension::REPOSITORY_ID),
                new Reference($config['printer']),
                $config['ignorePatternAliases'],
                $config['filters'] ?? null,
            ]
        );
        $serviceDefinition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG);

        $container->setDefinition('unused_step_definitions_checker', $serviceDefinition);

        $container->setDefinition(
            'unused_step_definitions_printer',
            new Definition(
                ConsoleUnusedStepDefinitionsPrinter::class,
                [new Reference(CliExtension::OUTPUT_ID)]
            )
        );
    }
}

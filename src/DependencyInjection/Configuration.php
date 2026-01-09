<?php

declare(strict_types=1);

namespace Sylius\LegacyShopBridgePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_legacy_shop_bridge');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('event')
            ->children()
                ->booleanNode('use_webpack')->defaultTrue()->end()
                ->arrayNode('events')
                    ->useAttributeAsKey('event_name')
                    ->arrayPrototype()
                        ->fixXmlConfig('block')
                        ->children()
                            ->arrayNode('blocks')
                                ->defaultValue([])
                                ->useAttributeAsKey('block_name')
                                ->arrayPrototype()
                                    ->canBeDisabled()
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(static fn (?string $template): array => ['template' => $template])
                                    ->end()
                                    ->children()
                                        ->booleanNode('enabled')->defaultNull()->end()
                                        ->arrayNode('context')->addDefaultsIfNotSet()->ignoreExtraKeys(false)->end()
                                        ->scalarNode('template')->defaultNull()->end()
                                        ->integerNode('priority')->defaultNull()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

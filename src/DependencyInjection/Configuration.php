<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@fintel.bz>
 * Date: 10/05/19
 * Time: 16.57
 */

namespace Szopen\SkebbyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('szopen_skebby');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('username')
                    ->defaultNull()->end()
                ->scalarNode('password')
                    ->defaultNull()->end()
                ->enumNode('auth_type')
                    ->values(['token', 'session'])
                    ->defaultValue('token')->end()
                ->enumNode('default_message_type')
                    ->values(['GP', 'TI', 'SI'])
                    ->defaultValue('TI')->end()
                ->scalarNode('default_sender_alias')
                    ->defaultValue("")->end()
            ->end();

        return $treeBuilder;
    }
}
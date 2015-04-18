<?php
namespace Mindweb\TrackerKernelStandard\Subscriber;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $this->buildSubscribers($treeBuilder);

        return $treeBuilder;
    }

    /**
     * @param $treeBuilder
     */
    private function buildSubscribers(TreeBuilder $treeBuilder)
    {
        $rootNode = $treeBuilder->root('subscribers');

        $rootNode
            ->useAttributeAsKey('subscriber')
                ->prototype('array')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }
}
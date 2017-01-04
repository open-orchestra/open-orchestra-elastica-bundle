<?php

namespace OpenOrchestra\ElasticaAdminBundle\DependencyInjection;

use OpenOrchestra\ElasticaFront\DisplayBlock\ElasticaListStrategy;
use OpenOrchestra\ElasticaFront\DisplayBlock\ElasticaSearchStrategy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenOrchestraElasticaAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $container->setParameter('open_orchestra_elastica.orchestra_choice.front_language', $container->getParameter('open_orchestra_backoffice.orchestra_choice.front_language'));
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach (array('generate_form', 'block_parameter') as $file) {
            $loader->load($file . '.yml');
        }

        $this->updateBlockParameter($container);
        $this->updateBlockConfiguration($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function updateBlockParameter(ContainerBuilder $container)
    {
        $blockType = array(
            ElasticaListStrategy::NAME,
            ElasticaSearchStrategy::NAME,
        );

        $blocksAlreadySet = array();
        if ($container->hasParameter('open_orchestra.blocks')) {
            $blocksAlreadySet = $container->getParameter('open_orchestra.blocks');
        }
        $blocks = array_merge($blocksAlreadySet, $blockType);
        $container->setParameter('open_orchestra.blocks', $blocks);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function updateBlockConfiguration(ContainerBuilder $container)
    {
        $searchBlockConfiguration = array(
            ElasticaListStrategy::NAME => array(
                'category' => 'open_orchestra_elastica_admin.block_configuration.category.search',
            ),
            ElasticaSearchStrategy::NAME => array(
                'category' => 'open_orchestra_elastica_admin.block_configuration.category.search',
            ),
        );

        $blockConfiguration = array();
        if ($container->hasParameter('open_orchestra_backoffice.block_configuration')) {
            $blockConfiguration = $container->getParameter('open_orchestra_backoffice.block_configuration');
        }
        $blockConfiguration = array_merge($blockConfiguration, $searchBlockConfiguration);
        $container->setParameter('open_orchestra_backoffice.block_configuration', $blockConfiguration);
    }
}

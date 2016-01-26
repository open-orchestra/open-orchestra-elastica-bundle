<?php

namespace OpenOrchestra\ElasticaAdmin\DisplayIcon;

use OpenOrchestra\BackofficeBundle\DisplayIcon\Strategies\AbstractStrategy;

/**
 * Class ElasticaSearchStrategy
 */
class ElasticaSearchStrategy extends AbstractStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param string $block
     *
     * @return boolean
     */
    public function support($block)
    {
        return in_array($block, array ('elastica_search', 'elastica_list'));
    }

    /**
     * Perform the show action for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraElasticaAdminBundle:Block/Search:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'elastica_search';
    }
}

<?php

namespace OpenOrchestra\ElasticaAdmin\DisplayIcon;

use OpenOrchestra\Backoffice\DisplayIcon\Strategies\AbstractStrategy;
use OpenOrchestra\ElasticaFront\DisplayBlock\ElasticaListStrategy as BaseElasticaListStrategy;

/**
 * Class ElasticaListStrategy
 */
class ElasticaListStrategy extends AbstractStrategy
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
        return BaseElasticaListStrategy::NAME === $block;
    }

    /**
     * Perform the show action for a block
     *
     * @return string
     */
    public function show()
    {
        return $this->render('OpenOrchestraElasticaAdminBundle:Block/List:showIcon.html.twig');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'elastica_list';
    }
}

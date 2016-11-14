<?php

namespace OpenOrchestra\ElasticaAdmin\DisplayBlock;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ElasticaFront\DisplayBlock\ElasticaSearchStrategy as BaseElasticaSearchStrategy;

/**
 * Class ElasticaSearchStrategy
 */
class ElasticaSearchStrategy extends AbstractStrategy
{
    /**
     * Check if the strategy support this block
     *
     * @param ReadBlockInterface $block
     *
     * @return boolean
     */
    public function support(ReadBlockInterface $block)
    {
        return BaseElasticaSearchStrategy::NAME === $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param ReadBlockInterface $block
     *
     * @return Response
     */
    public function show(ReadBlockInterface $block)
    {
        return $this->render('OpenOrchestraElasticaAdminBundle:Block/Search:show.html.twig', array(
            'id' => $block->getId(),
            'style' => $block->getStyle(),
        ));
    }

    /**
     * @param ReadBlockInterface $block
     *
     * @return array
     */
    public function getCacheTags(ReadBlockInterface $block)
    {
        return array();
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

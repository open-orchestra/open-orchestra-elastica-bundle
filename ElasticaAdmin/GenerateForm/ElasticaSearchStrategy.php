<?php

namespace OpenOrchestra\ElasticaAdmin\GenerateForm;

use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class ElasticaSearchStrategy
 */
class ElasticaSearchStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return 'elastica_search' === $block->getComponent();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'elastica_search';
    }
}

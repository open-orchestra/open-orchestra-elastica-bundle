<?php

namespace OpenOrchestra\ElasticaAdmin\BlockParameter;

use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Class ElasticSearchStrategy
 */
class ElasticSearchStrategy implements BlockParameterInterface
{
    const NAME = 'elastica_search';

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return in_array($block->getComponent(), array (self::NAME, 'elastica_list'));
    }

    /**
     * @return array
     */
    public function getBlockParameter()
    {
        return array('request'.self::NAME);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'elastica_search';
    }
}

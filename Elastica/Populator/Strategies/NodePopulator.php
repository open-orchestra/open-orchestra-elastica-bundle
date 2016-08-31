<?php

namespace OpenOrchestra\Elastica\Populator\Strategies;

use OpenOrchestra\Elastica\Indexor\MultipleDocumentIndexorInterface;
use OpenOrchestra\Elastica\Populator\ElasticaPopulatorInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;

/**
 * Class NodePopulator
 */
class NodePopulator implements ElasticaPopulatorInterface
{
    protected $multipleIndexor;
    protected $nodeRepository;

    /**
     * @param MultipleDocumentIndexorInterface $multipleIndexor
     * @param ReadNodeRepositoryInterface      $nodeRepository
     */
    public function __construct(MultipleDocumentIndexorInterface $multipleIndexor, ReadNodeRepositoryInterface $nodeRepository)
    {
        $this->multipleIndexor = $multipleIndexor;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Populate nodes
     */
    public function populate()
    {
        $skip = 0;
        $limit = 20;
        while (!empty($nodes = $this->nodeRepository->findAllCurrentlyPublishedByTypeWithSkipAndLimit(NodeInterface::TYPE_DEFAULT, $skip, $limit)))  {
            $skip += $limit;
            $this->multipleIndexor->indexMultiple($nodes);
        }
    }
}

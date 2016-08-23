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
        $nodes = $this->nodeRepository->findAllCurrentlyPublishedByType(NodeInterface::TYPE_DEFAULT);

        $this->multipleIndexor->indexMultiple($nodes);
    }
}

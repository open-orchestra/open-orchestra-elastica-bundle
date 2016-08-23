<?php

namespace OpenOrchestra\Elastica\Transformer;

use Elastica\Document;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\ReadAreaInterface;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer implements ModelToElasticaTransformerInterface
{
    protected $displayBlockManager;
    protected $nodeRepository;

    /**
     * @param DisplayBlockManager         $displayBlockManager
     * @param ReadNodeRepositoryInterface $nodeRepository
     */
    public function __construct(DisplayBlockManager $displayBlockManager, ReadNodeRepositoryInterface $nodeRepository)
    {
        $this->displayBlockManager = $displayBlockManager;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Transform the object to be indexed by Elasticsearch
     *
     * @param ReadNodeInterface $node
     *
     * @return Document
     */
    public function transform($node)
    {
        $blocksData = $this->transformBlock($node);
        $documentData = array(
            'id' => $node->getNodeId() . '-' . $node->getLanguage(). '-' . $node->getSiteId(),
            'elementId' => $node->getId(),
            'siteId' => $node->getSiteId(),
            'language' => $node->getLanguage(),
            'updatedAt' => $node->getUpdatedAt()->getTimestamp(),
            'name' => $node->getName(),
            'blocks' => $blocksData
        );

        $document = new Document($documentData['id'], $documentData);

        return $document;
    }


    /**
     * @param ReadNodeInterface $node
     *
     * @return array
     */
    protected function transformBlock(ReadNodeInterface $node)
    {
        $blocksData = array();
        $blocks = array_merge($this->getTransverseBlock($node->getRootArea(), $node), $node->getBlocks()->toArray());
        /** @var ReadBlockInterface $block */
        foreach ($blocks as $block) {
            $contentBlock = $this->displayBlockManager->toString($block);
            if (null !== $contentBlock) {
                $blocksData[] = array(
                    'type' => $block->getComponent(),
                    'content' => $contentBlock,
                );
            }
        }

        return $blocksData;
    }

    /**
     * @param ReadAreaInterface $area
     * @param ReadNodeInterface $node
     * @return array
     */
    protected function getTransverseBlock(ReadAreaInterface $area, ReadNodeInterface $node)
    {
        $blocks = array();
        $nodeTransverse = null;
        /** @var ReadAreaInterface $subArea */
        foreach ($area->getAreas() as $subArea) {
            /** @var ReadBlockInterface $block */
            foreach ($subArea->getBlocks() as $block) {
                if (isset($block['nodeId']) &&
                    isset($block['blockId']) &&
                    0 !== $block['nodeId']
                ) {
                    if (null === $nodeTransverse) {
                        $nodeTransverse = $this->nodeRepository->findOneCurrentlyPublished($block['nodeId'], $node->getLanguage(), $node->getSiteId());
                    }
                    $blocks[] = $nodeTransverse->getBlocks()->get($block['blockId']);
                }
            }
            if (count($subArea->getAreas()) > 0) {
                $blocks = array_merge($this->getTransverseBlock($subArea, $node), $blocks);
            }
        }

        return $blocks;
    }

}

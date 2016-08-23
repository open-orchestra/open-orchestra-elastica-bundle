<?php

namespace OpenOrchestra\Elastica\Transformer;

use Elastica\Document;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;

/**
 * Class NodeTransformer
 */
class NodeTransformer implements ModelToElasticaTransformerInterface
{
    protected $displayBlockManager;

    /**
     * @param DisplayBlockManager $displayBlockManager
     */
    public function __construct(DisplayBlockManager $displayBlockManager)
    {
        $this->displayBlockManager = $displayBlockManager;
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
        /** @var ReadBlockInterface $block */
        foreach ($node->getBlocks() as $block) {
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



}

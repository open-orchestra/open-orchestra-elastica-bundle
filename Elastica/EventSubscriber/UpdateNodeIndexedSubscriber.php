<?php

namespace OpenOrchestra\Elastica\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\NodeIndexor;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;

/**
 * Class UpdateNodeIndexedSubscriber
 */
class UpdateNodeIndexedSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $nodeIndexor;

    /**
     * @param ReadNodeRepositoryInterface $nodeRepository
     * @param NodeIndexor                 $nodeIndexor
     */
    public function __construct(ReadNodeRepositoryInterface $nodeRepository, NodeIndexor $nodeIndexor)
    {
        $this->nodeRepository = $nodeRepository;
        $this->nodeIndexor = $nodeIndexor;
    }

    /**
     * @param NodeEvent $event
     *
     * @throws IndexorWrongParameterException
     */
    public function updateIndexedNode(NodeEvent $event)
    {
        $node = $event->getNode();
        $lastPublishedNode = $this->nodeRepository->findOneCurrentlyPublished($node->getNodeId(), $node->getLanguage(), $node->getSiteId());
        if ($lastPublishedNode instanceof ReadNodeInterface) {
            $this->nodeIndexor->index($lastPublishedNode);
        } else {
            $this->nodeIndexor->delete($node);
        }
    }

    /**
     * @param NodeEvent $event
     *
     * @throws IndexorWrongParameterException
     */
    public function deleteIndexedNode(NodeEvent $event)
    {
        $this->nodeIndexor->delete($event->getNode());
    }

    /**
     * @param NodeEvent $event
     *
     * @throws IndexorWrongParameterException
     */
    public function restoreIndexedNode(NodeEvent $event)
    {
        $this->nodeIndexor->index($event->getNode());
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'updateIndexedNode',
            NodeEvents::NODE_DELETE        => 'deleteIndexedNode',
            NodeEvents::NODE_RESTORE       => 'restoreIndexedNode',
        );
    }
}

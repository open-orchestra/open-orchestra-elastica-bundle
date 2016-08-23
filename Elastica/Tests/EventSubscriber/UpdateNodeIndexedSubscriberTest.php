<?php

namespace OpenOrchestra\Elastica\Tests\EventSubscriber;

use OpenOrchestra\Elastica\EventSubscriber\UpdateNodeIndexedSubscriber;
use OpenOrchestra\Elastica\Indexor\NodeIndexor;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Phake;

/**
 * Class UpdateNodeIndexedSubscriberTest
 */
class UpdateNodeIndexedSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateNodeIndexedSubscriber
     */
    protected $subscriber;

    protected $indexor;
    protected $nodeRepository;
    protected $node;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock(ReadNodeRepositoryInterface::CLASS);
        $this->indexor = Phake::mock(NodeIndexor::CLASS);
        $this->node = Phake::mock(ReadNodeInterface::CLASS);
        $this->event = Phake::mock(NodeEvent::CLASS);
        Phake::when($this->event)->getNode()->thenReturn($this->node);

        $this->subscriber = new UpdateNodeIndexedSubscriber($this->nodeRepository, $this->indexor);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(EventSubscriberInterface::CLASS, $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testSubscribedEVents()
    {
        $this->assertArrayHasKey(NodeEvents::NODE_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_DELETE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(NodeEvents::NODE_RESTORE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test update indexed node
     */
    public function testUpdateIndexedNode()
    {
        $publishedNode = Phake::mock(ReadNodeInterface::CLASS);
        Phake::when($this->nodeRepository)->findOneCurrentlyPublished(Phake::anyParameters())->thenReturn($publishedNode);

        $this->subscriber->updateIndexedNode($this->event);

        Phake::verify($this->indexor)->index($publishedNode);
    }

    /**
     * Test update index with no published document
     */
    public function testUpdateIndexedNodeWithNoPublishedNode()
    {
        Phake::when($this->nodeRepository)->findOneCurrentlyPublished(Phake::anyParameters())->thenReturn(null);

        $this->subscriber->updateIndexedNode($this->event);

        Phake::verify($this->indexor)->delete($this->node);
    }

    /**
     * Test delete indexed node
     */
    public function testDeleteIndexedNode()
    {
        $this->subscriber->deleteIndexedNode($this->event);

        Phake::verify($this->indexor)->delete($this->node);
    }

    /**
     * Test restore indexed node
     */
    public function testRestoreIndexedNode()
    {
        $this->subscriber->restoreIndexedNode($this->event);

        Phake::verify($this->indexor)->index($this->node);
    }
}

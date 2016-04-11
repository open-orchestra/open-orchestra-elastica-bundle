<?php

namespace OpenOrchestra\Elastica\Tests\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\ContentIndexor;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use Phake;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\Elastica\EventSubscriber\UpdateContentIndexedSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateContentIndexedSubscriberTest
 */
class UpdateContentIndexedSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UpdateContentIndexedSubscriber
     */
    protected $subscriber;

    protected $indexor;
    protected $contentRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock(ContentRepositoryInterface::CLASS);
        $this->indexor = Phake::mock(ContentIndexor::CLASS);

        $this->subscriber = new UpdateContentIndexedSubscriber($this->contentRepository, $this->indexor);
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
        $this->assertArrayHasKey(ContentEvents::CONTENT_CHANGE_STATUS, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_DELETE, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(ContentEvents::CONTENT_RESTORE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test update indexed content
     */
    public function testUpdateIndexedContent()
    {
        $content = Phake::mock(ContentInterface::CLASS);
        $event = Phake::mock(ContentEvent::CLASS);
        Phake::when($event)->getContent()->thenReturn($content);

        $publishedContent = Phake::mock(ContentInterface::CLASS);
        Phake::when($this->contentRepository)->findLastPublishedVersion(Phake::anyParameters())->thenReturn($publishedContent);

        $this->subscriber->updateIndexedContent($event);

        Phake::verify($this->indexor)->index($publishedContent);
    }

    /**
     * Test update index with no published document
     */
    public function testUpdateIndexedContentWithNoPublishedContent()
    {
        $content = Phake::mock(ContentInterface::CLASS);
        $event = Phake::mock(ContentEvent::CLASS);
        Phake::when($event)->getContent()->thenReturn($content);

        Phake::when($this->contentRepository)->findLastPublishedVersion(Phake::anyParameters())->thenReturn(null);

        $this->subscriber->updateIndexedContent($event);

        Phake::verify($this->indexor)->delete($content);
    }

    /**
     * Test delete indexed content
     */
    public function testDeleteIndexedContent()
    {
        $content = Phake::mock(ContentInterface::CLASS);
        $event = Phake::mock(ContentEvent::CLASS);
        Phake::when($event)->getContent()->thenReturn($content);

        $this->subscriber->deleteIndexedContent($event);

        Phake::verify($this->indexor)->delete($content);
    }

    /**
     * Test restore indexed content
     */
    public function testRestoreIndexedContent()
    {
        $content = Phake::mock(ContentInterface::CLASS);
        $event = Phake::mock(ContentEvent::CLASS);
        Phake::when($event)->getContent()->thenReturn($content);

        $this->subscriber->restoreIndexedContent($event);

        Phake::verify($this->indexor)->index($content);
    }
}

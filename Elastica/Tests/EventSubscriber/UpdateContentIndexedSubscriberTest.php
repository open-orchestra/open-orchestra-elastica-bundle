<?php

namespace OpenOrchestra\Elastica\Tests\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\ContentIndexor;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
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
    }

    /**
     * @param bool $published
     * @param int  $version
     * @param int  $indexTime
     *
     * @dataProvider provideContentPublishVersionAndIndexTime
     */
    public function testUpdateIndexedContent($published, $version, $indexTime)
    {
        $status = Phake::mock(StatusInterface::CLASS);
        Phake::when($status)->isPublished()->thenReturn($published);
        $content = Phake::mock(ContentInterface::CLASS);
        Phake::when($content)->getVersion()->thenReturn($version);
        Phake::when($content)->getStatus()->thenReturn($status);
        $event = Phake::mock(ContentEvent::CLASS);
        Phake::when($event)->getContent()->thenReturn($content);

        $publishedContent = Phake::mock(ContentInterface::CLASS);
        Phake::when($publishedContent)->getVersion()->thenReturn(2);
        Phake::when($this->contentRepository)->findLastPublishedVersionByContentIdAndLanguage(Phake::anyParameters())->thenReturn($publishedContent);

        $this->subscriber->updateIndexedContent($event);

        Phake::verify($this->indexor, Phake::times($indexTime))->index($content);
    }

    /**
     * @return array
     */
    public function provideContentPublishVersionAndIndexTime()
    {
        return array(
            'Content not published and olderr' => array(false, 1, 0),
            'Content not published and same' => array(false, 2, 0),
            'Content not published and newer' => array(false, 3, 0),
            'Content published and older' => array(true, 1, 0),
            'Content published and same' => array(true, 2, 1),
            'Content published and newer' => array(true, 3, 1),
        );
    }
}

<?php

namespace OpenOrchestra\Elastica\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\ContentIndexor;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateContentIndexedSubscriber
 */
class UpdateContentIndexedSubscriber implements EventSubscriberInterface
{
    protected $contentRepository;
    protected $contentIndexor;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param ContentIndexor             $contentIndexor
     */
    public function __construct(ContentRepositoryInterface $contentRepository, ContentIndexor $contentIndexor)
    {
        $this->contentRepository = $contentRepository;
        $this->contentIndexor = $contentIndexor;
    }

    /**
     * @param ContentEvent $event
     */
    public function updateIndexedContent(ContentEvent $event)
    {
        $content = $event->getContent();

        if (!$content->getStatus()->isPublished()) {
            return;
        }

        $lastPublishedContent = $this->contentRepository->findLastPublishedVersionByContentIdAndLanguage($content->getContentId(), $content->getLanguage());

        if ($content->getVersion() >= $lastPublishedContent->getVersion()) {
            $this->contentIndexor->index($content);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_CHANGE_STATUS => 'updateIndexedContent',
        );
    }
}

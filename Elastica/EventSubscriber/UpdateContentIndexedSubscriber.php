<?php

namespace OpenOrchestra\Elastica\EventSubscriber;

use OpenOrchestra\Elastica\Indexor\ContentIndexor;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;

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
     *
     * @throws IndexorWrongParameterException
     */
    public function updateIndexedContent(ContentEvent $event)
    {
        $content = $event->getContent();

        $lastPublishedContent = $this->contentRepository->findLastPublishedVersion($content->getContentId(), $content->getLanguage());

        if ($lastPublishedContent instanceof ContentInterface) {
            $this->contentIndexor->index($lastPublishedContent);
        } else {
            $this->contentIndexor->delete($content);
        }
    }

    /**
     * @param ContentEvent $event
     *
     * @throws IndexorWrongParameterException
     */
    public function deleteIndexedContent(ContentEvent $event)
    {
        $this->contentIndexor->delete($event->getContent());
    }

    /**
     * @param ContentEvent $event
     *
     * @throws IndexorWrongParameterException
     */
    public function restoreIndexedContent(ContentEvent $event)
    {
        $this->contentIndexor->index($event->getContent());
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_CHANGE_STATUS => 'updateIndexedContent',
            ContentEvents::CONTENT_DELETE        => 'deleteIndexedContent',
            ContentEvents::CONTENT_RESTORE       => 'restoreIndexedContent',
        );
    }
}

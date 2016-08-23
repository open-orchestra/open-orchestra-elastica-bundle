<?php

namespace OpenOrchestra\Elastica\Indexor;

use Elastica\Exception\NotFoundException;
use Elastica\Index;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class ContentIndexor
 */
class ContentIndexor extends AbstractDocumentIndexor
{
    /**
     * @param ContentInterface $content
     * @param Index            $index
     */
    protected function indexDocument($content, Index $index)
    {
        $type = $index->getType('content_' . $content->getContentType());
        $document = $this->transformer->transform($content);
        $type->addDocument($document);
    }

    /**
     * @param ContentInterface $content
     *
     * @throws IndexorWrongParameterException
     */
    public function delete($content)
    {
        if (!$content instanceof ContentInterface) {
            throw new IndexorWrongParameterException();
        }

        $index = $this->client->getIndex($this->indexName);
        $type = $index->getType('content_' . $content->getContentType());
        $document = $this->transformer->transform($content);
        try {
            $type->deleteDocument($document);
            $index->refresh();
        } catch (NotFoundException $e) {

        }
    }
}

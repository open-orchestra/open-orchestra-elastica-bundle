<?php

namespace OpenOrchestra\Elastica\Indexor;

use Elastica\Client;
use Elastica\Exception\NotFoundException;
use Elastica\Index;
use OpenOrchestra\Elastica\Exception\IndexorWrongParameterException;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class ContentIndexor
 */
class ContentIndexor implements DocumentIndexorInterface, MultipleDocumentIndexorInterface, DocumentDeletorInterface
{
    protected $client;
    protected $transformer;

    /**
     * @param Client                              $client
     * @param ModelToElasticaTransformerInterface $transformer
     */
    public function __construct(Client $client, ModelToElasticaTransformerInterface $transformer)
    {
        $this->client = $client;
        $this->transformer = $transformer;
    }

    /**
     * Index the object after a transformation
     *
     * @param ContentInterface $content
     */
    public function index($content)
    {
        $index = $this->client->getIndex('content');

        $this->indexDocument($content, $index);

        $index->refresh();
    }

    /**
     * Index the object after a transformation
     *
     * @param array $contents
     */
    public function indexMultiple(array $contents)
    {
        $index = $this->client->getIndex('content');

        foreach ($contents as $content) {
            $this->indexDocument($content, $index);
        }

        $index->refresh();
    }

    /**
     * @param ContentInterface $content
     * @param Index            $index
     */
    protected function indexDocument(ContentInterface $content, Index $index)
    {
        $type = $index->getType('content_' . $content->getContentType());
        $document = $this->transformer->transform($content);
        $type->addDocument($document);
    }

    /**
     * @param mixed $content
     *
     * @throws IndexorWrongParameterException
     */
    public function delete($content)
    {
        if (!$content instanceof ContentInterface) {
            throw new IndexorWrongParameterException();
        }

        $index = $this->client->getIndex('content');
        $type = $index->getType('content_' . $content->getContentType());
        $document = $this->transformer->transform($content);
        try {
            $type->deleteDocument($document);
            $index->refresh();
        } catch (NotFoundException $e) {

        }
    }
}

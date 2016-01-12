<?php

namespace OpenOrchestra\Elastica\SchemaGenerator;

use Elastica\Client;
use OpenOrchestra\Elastica\Mapper\FieldToElasticaTypeMapper;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;

/**
 * Class ContentTypeSchemaGenerator
 */
class ContentTypeSchemaGenerator implements DocumentToElasticaSchemaGeneratorInterface
{
    protected $client;
    protected $formMapper;

    /**
     * @param Client                   $client
     * @param FieldToElasticaTypeMapper $formMapper
     */
    public function __construct(Client $client, FieldToElasticaTypeMapper $formMapper)
    {
        $this->client = $client;
        $this->formMapper = $formMapper;
    }

    /**
     * Create a elasticSearch linked to the object
     *
     * @param ContentTypeInterface $contentType
     */
    public function createMaping($contentType)
    {
        $index = $this->client->getIndex('content');
        $type = $index->getType('content_' . $contentType->getContentTypeId());

        $mapping = array(
            'id' => array('type' => 'string', 'include_in_all' => true),
            'elementId' => array('type' => 'string', 'include_in_all' => true),
            'contentId' => array('type' => 'string', 'include_in_all' => true),
            'name' => array('type' => 'string', 'include_in_all' => true),
            'siteId' => array('type' => 'string', 'include_in_all' => true),
            'language' => array('type' => 'string', 'include_in_all' => true),
            'contentType' => array('type' => 'string', 'include_in_all' => true),
        );

        /** @var FieldTypeInterface $field */
        foreach ($contentType->getFields() as $field) {
            if ($field->isSearchable()) {
                $mapping['attribute_' . $field->getFieldId()] = array('type' => $this->formMapper->map($field->getType()), 'include_in_all' => false);
                $mapping['attribute_' . $field->getFieldId() . '_stringValue'] = array('type' => 'string', 'include_in_all' => false);
            }
        }

        $type->setMapping($mapping);
    }
}

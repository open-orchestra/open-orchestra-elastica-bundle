<?php

namespace OpenOrchestra\Elastica\Tests\SchemaGenerator;

use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Client;
use Elastica\Index;
use Elastica\Type;
use OpenOrchestra\Elastica\Mapper\FormToElasticaTypeMapper;
use OpenOrchestra\Elastica\SchemaGenerator\ContentTypeSchemaGenerator;
use OpenOrchestra\Elastica\SchemaGenerator\DocumentToElasticaSchemaGeneratorInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\FieldTypeInterface;
use Phake;

/**
 * Test ContentTypeSchemaGeneratorTest
 */
class ContentTypeSchemaGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeSchemaGenerator
     */
    protected $schemaGenerator;

    protected $type;
    protected $index;
    protected $client;
    protected $formMapper;
    protected $elasticaType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->elasticaType = 'foo';
        $this->formMapper = Phake::mock(FormToElasticaTypeMapper::CLASS);
        Phake::when($this->formMapper)->map(Phake::anyParameters())->thenReturn($this->elasticaType);

        $this->type = Phake::mock(Type::CLASS);
        $this->index = Phake::mock(Index::CLASS);
        Phake::when($this->index)->getType(Phake::anyParameters())->thenReturn($this->type);
        $this->client = Phake::mock(Client::CLASS);
        Phake::when($this->client)->getIndex(Phake::anyParameters())->thenReturn($this->index);

        $this->schemaGenerator = new ContentTypeSchemaGenerator($this->client, $this->formMapper);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DocumentToElasticaSchemaGeneratorInterface::CLASS, $this->schemaGenerator);
    }

    /**
     * @param string $fieldType
     *
     * @dataProvider provideFieldTypeAndIndexedFieldType
     */
    public function testCreateMapping($fieldType)
    {
        $field1 = Phake::mock(FieldTypeInterface::CLASS);
        Phake::when($field1)->getFieldId()->thenReturn('fieldId1');
        Phake::when($field1)->isSearchable()->thenReturn(true);
        Phake::when($field1)->getType()->thenReturn($fieldType);
        $field2 = Phake::mock(FieldTypeInterface::CLASS);
        Phake::when($field2)->getFieldId()->thenReturn('fieldId2');
        Phake::when($field2)->isSearchable()->thenReturn(false);
        Phake::when($field2)->getType()->thenReturn('text');

        $fields = new ArrayCollection(array($field1, $field2));

        $contentType = Phake::mock(ContentTypeInterface::CLASS);
        Phake::when($contentType)->getContentTypeId()->thenReturn('contentTypeId');
        Phake::when($contentType)->getFields()->thenReturn($fields);

        $this->schemaGenerator->createMaping($contentType);

        Phake::verify($this->client)->getIndex('content');
        Phake::verify($this->index)->getType('content_contentTypeId');
        Phake::verify($this->type)->setMapping(array(
            'id' => array('type' => 'string', 'include_in_all' => true),
            'elementId' => array('type' => 'string', 'include_in_all' => true),
            'contentId' => array('type' => 'string', 'include_in_all' => true),
            'name' => array('type' => 'string', 'include_in_all' => true),
            'siteId' => array('type' => 'string', 'include_in_all' => true),
            'language' => array('type' => 'string', 'include_in_all' => true),
            'contentType' => array('type' => 'string', 'include_in_all' => true),
            'attribute_fieldId1' => array('type' => $this->elasticaType, 'include_in_all' => false),
            'attribute_fieldId1_stringValue' => array('type' => 'string', 'include_in_all' => false),
        ));
        Phake::verify($field1)->isSearchable();
        Phake::verify($field2)->isSearchable();
        Phake::verify($this->formMapper)->map($fieldType);
    }

    /**
     * @return array
     */
    public function provideFieldTypeAndIndexedFieldType()
    {
        return array(
            'Field type text' => array('text'),
            'Field type textarea' => array('textarea'),
            'Field type tinyMce' => array('oo_tinymce'),
        );
    }
}

<?php

namespace OpenOrchestra\Elastica\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Document;
use OpenOrchestra\Elastica\Transformer\ContentTransformer;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Phake;

/**
 * Test ContentTransformerTest
 */
class ContentTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTransformer
     */
    protected $transformer;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->transformer = new ContentTransformer();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(ModelToElasticaTransformerInterface::CLASS, $this->transformer);
    }

    /**
     * Test transformation
     */
    public function testTransform()
    {
        $attribute = Phake::mock(ContentAttributeInterface::CLASS);
        Phake::when($attribute)->getName()->thenReturn('attributeName');
        Phake::when($attribute)->getValue()->thenReturn('attributeValue');
        Phake::when($attribute)->getStringValue()->thenReturn('stringValue');
        $attribute2 = Phake::mock(ContentAttributeInterface::CLASS);
        Phake::when($attribute2)->getName()->thenReturn('attributeName2');
        Phake::when($attribute2)->getValue()->thenReturn('attributeValue2');
        Phake::when($attribute2)->getStringValue()->thenReturn('stringValue2');

        $attributes = new ArrayCollection();
        $attributes->add($attribute);
        $attributes->add($attribute2);

        $content = Phake::mock(ContentInterface::CLASS);
        Phake::when($content)->getId()->thenReturn('id');
        Phake::when($content)->getContentId()->thenReturn('contentId');
        Phake::when($content)->getName()->thenReturn('name');
        Phake::when($content)->getSiteId()->thenReturn('siteId');
        Phake::when($content)->getLanguage()->thenReturn('language');
        Phake::when($content)->getContentType()->thenReturn('contentType');
        Phake::when($content)->getAttributes()->thenReturn($attributes);

        $document = $this->transformer->transform($content);

        $this->assertInstanceOf(Document::CLASS, $document);

        $this->assertSame('contentId-language', $document->getId());
        $this->assertSame(array(
            'id' => 'contentId-language',
            'elementId' => 'id',
            'contentId' => 'contentId',
            'name' => 'name',
            'siteId' => 'siteId',
            'language' => 'language',
            'contentType' => 'contentType',
            'attribute_attributeName' => 'attributeValue',
            'attribute_attributeName_stringValue' => 'stringValue',
            'attribute_attributeName2' => 'attributeValue2',
            'attribute_attributeName2_stringValue' => 'stringValue2',
        ), $document->getData());
    }
}

<?php

namespace OpenOrchestra\Elastica\Tests\Transformer;

use Elastica\Document;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;
use OpenOrchestra\Elastica\Transformer\NodeTransformer;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use Phake;

/**
 * Test NodeTransformerTest
 */
class NodeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeTransformer
     */
    protected $transformer;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->transformer = new NodeTransformer();
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
        $date = new \DateTime();

        $node = Phake::mock(ReadNodeInterface::CLASS);
        Phake::when($node)->getId()->thenReturn('id');
        Phake::when($node)->getNodeId()->thenReturn('nodeId');
        Phake::when($node)->getName()->thenReturn('name');
        Phake::when($node)->getSiteId()->thenReturn('siteId');
        Phake::when($node)->getLanguage()->thenReturn('language');
        Phake::when($node)->getUpdatedAt()->thenReturn($date);

        $document = $this->transformer->transform($node);

        $this->assertInstanceOf(Document::CLASS, $document);

        $this->assertSame('nodeId-language-siteId', $document->getId());
        $this->assertSame(array(
            'id' => 'nodeId-language-siteId',
            'elementId' => 'id',
            'siteId' => 'siteId',
            'language' => 'language',
            'updatedAt' => $date->getTimestamp(),
            'name' => 'name',
        ), $document->getData());
    }
}

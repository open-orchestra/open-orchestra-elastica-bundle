<?php

namespace OpenOrchestra\Elastica\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Document;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\Elastica\Transformer\ModelToElasticaTransformerInterface;
use OpenOrchestra\Elastica\Transformer\NodeTransformer;
use OpenOrchestra\ModelInterface\Model\ReadAreaInterface;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
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
    protected $displayBlockManager;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->displayBlockManager = Phake::mock(DisplayBlockManager::class);
        $this->transformer = new NodeTransformer($this->displayBlockManager);
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

        $block = Phake::mock(ReadBlockInterface::class);
        Phake::when($block)->getComponent()->thenReturn('fake_component');

        $rootArea = Phake::mock(ReadAreaInterface::class);
        $areaBlock = Phake::mock(ReadAreaInterface::class);
        Phake::when($areaBlock)->getBlocks()->thenReturn(array(array('nodeId' => 0, 'blockId' => '0')));

        Phake::when($rootArea)->getAreas()->thenReturn(new ArrayCollection(array($areaBlock)));

        Phake::when($this->displayBlockManager)->toString(Phake::anyParameters())->thenReturn('fakeToString');

        $node = Phake::mock(ReadNodeInterface::CLASS);
        Phake::when($node)->getId()->thenReturn('id');
        Phake::when($node)->getNodeId()->thenReturn('nodeId');
        Phake::when($node)->getName()->thenReturn('name');
        Phake::when($node)->getSiteId()->thenReturn('siteId');
        Phake::when($node)->getLanguage()->thenReturn('language');
        Phake::when($node)->getUpdatedAt()->thenReturn($date);
        Phake::when($node)->getRootArea()->thenReturn($rootArea);
        Phake::when($node)->getBlocks()->thenReturn(new ArrayCollection(array($block)));

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
            'blocks' => array(
                array(
                    'type' => 'fake_component',
                    'content' => 'fakeToString'
                )
            ),
        ), $document->getData());
    }
}

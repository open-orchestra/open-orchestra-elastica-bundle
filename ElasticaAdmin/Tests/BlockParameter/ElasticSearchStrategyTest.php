<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\BlockParameter;

use OpenOrchestra\Backoffice\BlockParameter\BlockParameterInterface;
use OpenOrchestra\ElasticaAdmin\BlockParameter\ElasticSearchStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Phake;

/**
 * Test ElasticSearchStrategyTest
 */
class ElasticSearchStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticSearchStrategy
     */
    protected $strategy;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy = new ElasticSearchStrategy();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(BlockParameterInterface::CLASS, $this->strategy);
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertSame('elastica_search', $this->strategy->getName());
    }

    /**
     * @param bool   $supports
     * @param string $component
     *
     * @dataProvider provideSupportsLinkedToBlockComponent
     */
    public function testSupport($supports, $component)
    {
        $block = Phake::mock(BlockInterface::CLASS);
        Phake::when($block)->getComponent()->thenReturn($component);

        $this->assertSame($supports, $this->strategy->support($block));
    }

    /**
     * @return array
     */
    public function provideSupportsLinkedToBlockComponent()
    {
        return array(
            'elastica search block' => array(true, 'elastica_search'),
            'elastica list block' => array(true, 'elastica_list'),
            'foo block' => array(false, 'foo'),
            'bar block' => array(false, 'bar'),
        );
    }

    /**
     * Test get block parameter
     */
    public function testGetBlockParameter()
    {
        $this->assertSame(array('request.elastica_search'), $this->strategy->getBlockParameter());
    }
}

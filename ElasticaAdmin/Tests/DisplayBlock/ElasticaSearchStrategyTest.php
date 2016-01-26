<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\DisplayBlock;

use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager;
use OpenOrchestra\ElasticaAdmin\DisplayBlock\ElasticaSearchStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Phake;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Test ElasticaSearchStrategyTest
 */
class ElasticaSearchStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticaSearchStrategy
     */
    protected $strategy;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy = new ElasticaSearchStrategy();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DisplayBlockInterface::CLASS, $this->strategy);
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
        $block = Phake::mock(ReadBlockInterface::CLASS);
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
     * Test show
     */
    public function testShow()
    {
        $templating = Phake::mock(EngineInterface::CLASS);
        $manager = Phake::mock(DisplayBlockManager::CLASS);
        Phake::when($manager)->getTemplating()->thenReturn($templating);
        $this->strategy->setManager($manager);

        $block = Phake::mock(ReadBlockInterface::CLASS);
        Phake::when($block)->getId()->thenReturn('id');
        Phake::when($block)->getClass()->thenReturn('class');

        $this->strategy->show($block);

        Phake::verify($templating)->renderResponse(
            'OpenOrchestraElasticaAdminBundle:Block/Search:show.html.twig',
            array('id' => 'id', 'class' => 'class'),
            null
        );
    }
}

<?php

namespace OpenOrchestra\ElasticaAdmin\Tests\DisplayIcon;

use OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayInterface;
use OpenOrchestra\BackofficeBundle\DisplayIcon\DisplayManager;
use OpenOrchestra\ElasticaAdmin\DisplayIcon\ElasticaSearchStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Phake;
use Symfony\Component\Templating\EngineInterface;

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
        $this->assertInstanceOf(DisplayInterface::CLASS, $this->strategy);
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
        $this->assertSame($supports, $this->strategy->support($component));
    }

    /**
     * @return array
     */
    public function provideSupportsLinkedToBlockComponent()
    {
        return array(
            'elastica search block' => array(true, 'elastica_search'),
            'elastica list block' => array(false, 'elastica_list'),
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
        $manager = Phake::mock(DisplayManager::CLASS);
        Phake::when($manager)->getTemplating()->thenReturn($templating);
        $this->strategy->setManager($manager);

        $this->strategy->show();

        Phake::verify($templating)->render(
            'OpenOrchestraElasticaAdminBundle:Block/Search:showIcon.html.twig',
            array()
        );
    }
}

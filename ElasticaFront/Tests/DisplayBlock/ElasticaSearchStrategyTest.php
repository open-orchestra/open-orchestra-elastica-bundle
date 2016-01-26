<?php

namespace OpenOrchestra\ElasticaFront\Tests\DisplayBlock;

use OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockInterface;
use OpenOrchestra\ElasticaFront\DisplayBlock\ElasticaSearchStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Phake;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Test ElasticaSearchStrategyTest
 */
class ElasticaSearchStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticaSearchStrategy
     */
    protected $strategy;

    protected $formFactory;
    protected $request;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->formFactory = Phake::mock(FormFactory::CLASS);
        $this->request = Phake::mock(Request::CLASS);
        $requestStack = Phake::mock(RequestStack::CLASS);
        Phake::when($requestStack)->getCurrentRequest()->thenReturn($this->request);

        $this->strategy = new ElasticaSearchStrategy($this->formFactory, $requestStack);
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
            'elastica list block' => array(false, 'elastica_list'),
            'foo block' => array(false, 'foo'),
            'bar block' => array(false, 'bar'),
        );
    }
}

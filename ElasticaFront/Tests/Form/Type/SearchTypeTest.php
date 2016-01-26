<?php

namespace OpenOrchestra\ElasticaFront\Tests\Form\Type;

use OpenOrchestra\ElasticaFront\Form\Type\SearchType;
use Phake;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Test SearchTypeTest
 */
class SearchTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SearchType
     */
    protected $type;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->type = new SearchType();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(AbstractType::CLASS, $this->type);
    }

    /**
     * Test get name
     */
    public function testGetName()
    {
        $this->assertSame('elastica_search', $this->type->getName());
    }

    /**
     * Test configure options
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock(OptionsResolver::CLASS);

        $this->type->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    /**
     * Test build form
     */
    public function testBuildForm()
    {
        $builder = Phake::mock(FormBuilderInterface::CLASS);

        $this->type->buildForm($builder, array());

        Phake::verify($builder)->add('search', 'text', array('required' => false));
    }
}

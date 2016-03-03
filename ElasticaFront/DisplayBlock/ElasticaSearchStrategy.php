<?php

namespace OpenOrchestra\ElasticaFront\DisplayBlock;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\ElasticaFront\Form\Type\SearchType;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ElasticaSearchStrategy
 */
class ElasticaSearchStrategy extends AbstractStrategy
{
    protected $formFactory;
    protected $requestStack;

    /**
     * @param FormFactory  $formFactory
     * @param RequestStack $requestStack
     */
    public function __construct(FormFactory $formFactory, RequestStack $requestStack)
    {
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
    }

    /**
     * Check if the strategy support this block
     *
     * @param ReadBlockInterface $block
     *
     * @return boolean
     */
    public function support(ReadBlockInterface $block)
    {
        return 'elastica_search' == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param ReadBlockInterface $block
     *
     * @return Response
     */
    public function show(ReadBlockInterface $block)
    {
        $formData = $this->requestStack->getCurrentRequest()->get('elastica_search');
        $form = $this->formFactory->create(new SearchType(), $formData, array(
            'method' => 'GET',
        ));

        return $this->render('OpenOrchestraElasticaFrontBundle:Block/Search:show.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param ReadBlockInterface $block
     *
     * @return Array
     */
    public function getCacheTags(ReadBlockInterface $block)
    {
        return array();
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'elastica_search';
    }
}

<?php

namespace OpenOrchestra\ElasticaFront\DisplayBlock;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\ElasticaFront\Form\Type\SearchType;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ElasticaSearchStrategy
 */
class ElasticaSearchStrategy extends AbstractStrategy
{
    protected $router;
    protected $formFactory;
    protected $requestStack;
    protected $nodeRepository;
    protected $currentSiteManager;

    /**
     * @param FormFactory                 $formFactory
     * @param RequestStack                $requestStack
     * @param RouterInterface             $router
     * @param ReadNodeRepositoryInterface $nodeRepository
     * @param CurrentSiteIdInterface      $currentSiteManager
     */
    public function __construct(
        FormFactory $formFactory,
        RequestStack $requestStack,
        RouterInterface $router,
        ReadNodeRepositoryInterface $nodeRepository,
        CurrentSiteIdInterface $currentSiteManager
    ) {
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
        $this->nodeRepository = $nodeRepository;
        $this->currentSiteManager = $currentSiteManager;
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
        $formParameters = array('method' => 'GET');

        if ('' != $block->getAttribute('contentNodeId')) {
            $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
            $siteId = $this->currentSiteManager->getCurrentSiteId();
            $nodeId = $this->nodeRepository->findPublishedInLastVersion($block->getAttribute('contentNodeId'), $language, $siteId)->getId();
            $formParameters['action'] = $this->router->generate($nodeId);
        }

        $formData = $this->requestStack->getCurrentRequest()->get('elastica_search');
        $form = $this->formFactory->create(new SearchType(), $formData, $formParameters);

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

<?php

namespace OpenOrchestra\ElasticaFront\DisplayBlock;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractDisplayBlockStrategy;
use OpenOrchestra\DisplayBundle\Manager\ContextInterface;
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
class ElasticaSearchStrategy extends AbstractDisplayBlockStrategy
{
    const NAME = 'elastica_search';

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
     * @param ContextInterface            $currentSiteManager
     */
    public function __construct(
        FormFactory $formFactory,
        RequestStack $requestStack,
        RouterInterface $router,
        ReadNodeRepositoryInterface $nodeRepository,
        ContextInterface $currentSiteManager
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
        return self::NAME == $block->getComponent();
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
            $language = $this->currentSiteManager->getCurrentSiteLanguage();
            $siteId = $this->currentSiteManager->getCurrentSiteId();
            $nodeId = $this->nodeRepository->findOnePublished($block->getAttribute('contentNodeId'), $language, $siteId)->getId();
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
     * @return array
     */
    public function getCacheTags(ReadBlockInterface $block)
    {
        return array();
    }

    /**
     * @return array
     */
    public function getBlockParameter()
    {
        return array('request.elastica_search');
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

<?php

namespace OpenOrchestra\ElasticaFront\DisplayBlock;

use Elastica\Client;
use Elastica\Query;
use Elastica\QueryBuilder;
use Elastica\Search;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\AbstractStrategy;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ElasticaListStrategy
 */
class ElasticaListStrategy extends AbstractStrategy
{
    protected $requestStack;
    protected $client;

    /**
     * @param RequestStack $requestStack
     * @param Client       $client
     */
    public function __construct(RequestStack $requestStack, Client $client)
    {
        $this->requestStack = $requestStack;
        $this->client = $client;
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
        return 'elastica_list' == $block->getComponent();
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
        $request = $this->requestStack->getCurrentRequest();

        $data = $request->get('elastica_search');
        $queryMethod = 'match_all';
        $searchParameter = null;
        if (is_array($data) && array_key_exists('search', $data) && null != $data['search']) {
            $searchParameter = $data['search'];
            $queryMethod = 'query_string';
        }

        $index = $this->client->getIndex('content');

        $qb = new QueryBuilder();

        $search = new Search($this->client);
        $search->addIndex($index);
        $search->setQuery($qb->query()->filtered(
            $qb->query()->$queryMethod($searchParameter),
            $qb->filter()->bool()->addMust(
                $qb->filter()->term(array('language' => $request->getLocale()))
            )
        ));

        $searchData = $search->search(null, array('limit' => $block->getAttribute('searchLimit')));

        return $this->render('OpenOrchestraElasticaFrontBundle:Block/List:show.html.twig', array(
            'searchData' => $searchData,
        ));
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'elastica_list';
    }
}

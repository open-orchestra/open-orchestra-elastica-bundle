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
    const NAME = 'elastica_list';

    protected $requestStack;
    protected $indexName;
    protected $client;

    /**
     * @param RequestStack $requestStack
     * @param Client       $client
     * @param string       $indexName
     */
    public function __construct(RequestStack $requestStack, Client $client, $indexName)
    {
        $this->requestStack = $requestStack;
        $this->indexName = $indexName;
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
        $request = $this->requestStack->getCurrentRequest();

        $data = $request->get('elastica_search');
        $searchData = array();
        if (is_array($data) && array_key_exists('search', $data) && null != $data['search']) {
            $searchParameter = $data['search'];

            $index = $this->client->getIndex($this->indexName);

            $qb = new QueryBuilder();

            $search = new Search($this->client);
            $search->addIndex($index);
            $search->setQuery($qb->query()->filtered(
                $qb->query()->query_string($searchParameter),
                $qb->filter()->bool()->addMust(
                    $qb->filter()->term(array('language' => $request->getLocale()))
                )
            ));

            $searchData = $search->search(null, array('limit' => $block->getAttribute('searchLimit')));
        }

        return $this->render('OpenOrchestraElasticaFrontBundle:Block/List:show.html.twig', array(
            'searchData' => $searchData,
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
        return 'elastica_list';
    }
}

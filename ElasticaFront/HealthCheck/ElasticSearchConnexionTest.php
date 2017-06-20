<?php

namespace OpenOrchestra\ElasticaFront\HealthCheck;

use Elastica\Client;
use OpenOrchestra\BaseBundle\HealthCheck\AbstractHealthCheckTest;
use OpenOrchestra\BaseBundle\HealthCheck\HealthCheckTestResultInterface;

/**
 * Class ElasticSearchConnexionTest
 */
class ElasticSearchConnexionTest extends AbstractHealthCheckTest
{
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $label = "ElasticSearch DB connexion";
        if (false === $this->client->hasConnection()) {
            return $this->createTestResult(true, $label, HealthCheckTestResultInterface::ERROR);
        }

        return $this->createValidTestResult($label);
    }
}

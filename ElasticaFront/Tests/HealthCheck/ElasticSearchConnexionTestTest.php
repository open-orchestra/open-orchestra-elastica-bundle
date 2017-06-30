<?php

namespace OpenOrchestra\ElasticaFront\Tests\HealthCheck;

use Elastica\Client;
use OpenOrchestra\BaseBundle\HealthCheck\HealthCheckTestResult;
use OpenOrchestra\BaseBundle\HealthCheck\HealthCheckTestResultInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ElasticaFront\HealthCheck\ElasticSearchConnexionTest;
use Phake;

/**
 * Class ElasticSearchConnexionTestTest
 */
class ElasticSearchConnexionTestTest extends AbstractBaseTestCase
{
    /** @var ElasticSearchConnexionTest */
    protected $test;
    protected $client;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->client = Phake::mock(Client::class);

        $this->test = new ElasticSearchConnexionTest($this->client);
        $this->test->setHealthCheckResultClass(HealthCheckTestResult::class);
    }

    /**
     * @param bool $hasConnection
     * @param bool $error
     * @param int  $level
     *
     * @dataProvider provideRequestHeader
     */
    public function testRun($hasConnection, $error, $level)
    {
        Phake::when($this->client)->hasConnection()->thenReturn($hasConnection);

        $result = $this->test->run();
        $this->assertInstanceOf(HealthCheckTestResult::class, $result);
        $this->assertEquals($error, $result->isError());
        $this->assertEquals($level, $result->getLevel());
    }

    /**
     * @return array
     */
    public function provideRequestHeader()
    {
        return array(
            array(false, true, HealthCheckTestResultInterface::ERROR),
            array(true, false, HealthCheckTestResultInterface::OK),
        );
    }
}

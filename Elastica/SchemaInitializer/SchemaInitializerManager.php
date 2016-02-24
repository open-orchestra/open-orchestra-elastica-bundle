<?php

namespace OpenOrchestra\Elastica\SchemaInitializer;

/**
 * Class SchemaInitializerManager
 */
class SchemaInitializerManager implements ElasticaSchemaInitializer
{
    protected $initializers = array();

    /**
     * @param ElasticaSchemaInitializer $initializer
     */
    public function addInitializer(ElasticaSchemaInitializer $initializer)
    {
        $this->initializers[] = $initializer;
    }

    /**
     * Initialize everything
     */
    public function initialize()
    {
        /** @var ElasticaSchemaInitializer $initializer */
        foreach ($this->initializers as $initializer) {
            $initializer->initialize();
        }
    }
}

<?php

namespace OpenOrchestra\Elastica\SchemaInitializer;

@trigger_error('The '.__NAMESPACE__.'\ElasticaSchemaInitializer class is deprecated since version 1.2.0 and will be removed in 1.3.0, it is replace by ElasticaSchemaInitializerInterface', E_USER_DEPRECATED);

/**
 * Interface ElasticaSchemaInitializer
 *
 * @deprecated use the ElasticaSchemaInitializerInterface instead, will be removed in 1.3.0
 */
interface ElasticaSchemaInitializer
{
    /**
     * Initialize the elastica schema for an object
     */
    public function initialize();
}

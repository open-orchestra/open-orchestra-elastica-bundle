<?php

namespace OpenOrchestra\Elastica\Transformer;

use Elastica\Document;
use OpenOrchestra\ModelInterface\Model\ContentAttributeInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class ContentTransformer
 */
class ContentTransformer implements ModelToElasticaTransformerInterface
{
    /**
     * Transform the object to be indexed by Elasticsearch
     *
     * @param ContentInterface $content
     *
     * @return Document
     */
    public function transform($content)
    {
        $documentData = array(
            'id' => $content->getContentId() . '-' . $content->getLanguage(),
            'elementId' => $content->getId(),
            'contentId' => $content->getContentId(),
            'name' => $content->getName(),
            'siteId' => $content->getSiteId(),
            'language' => $content->getLanguage(),
            'contentType' => $content->getContentType(),
        );

        /** @var ContentAttributeInterface $attribute */
        foreach ($content->getAttributes() as $attribute) {
            $documentData['attribute_' . $attribute->getName()] = $attribute->getValue();
            $documentData['attribute_' . $attribute->getName() . '_stringValue'] = $attribute->getStringValue();
        }

        return new Document($documentData['id'], $documentData);
    }
}

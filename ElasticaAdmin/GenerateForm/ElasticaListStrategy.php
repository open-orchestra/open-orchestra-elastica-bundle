<?php

namespace OpenOrchestra\ElasticaAdmin\GenerateForm;

use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ElasticaListStrategy
 */
class ElasticaListStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return 'elastica_list' === $block->getComponent();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('searchLimit', 'integer', array(
            'label' => 'open_orchestra_elastica_admin.form.elastica_list.search_limit',
        ));
    }

    /**
     * Get the default configuration for the block
     *
     * @return array
     */
    public function getDefaultConfiguration()
    {
        return array('searchLimit' => 10);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'elastica_list';
    }
}

<?php

namespace OpenOrchestra\ElasticaAdmin\GenerateForm;

use OpenOrchestra\Backoffice\GenerateForm\Strategies\AbstractBlockStrategy;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ElasticaSearchStrategy
 */
class ElasticaSearchStrategy extends AbstractBlockStrategy
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block)
    {
        return 'elastica_search' === $block->getComponent();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contentNodeId', 'oo_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.content_list.node',
            'constraints' => new NotBlank(),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'elastica_search';
    }
}

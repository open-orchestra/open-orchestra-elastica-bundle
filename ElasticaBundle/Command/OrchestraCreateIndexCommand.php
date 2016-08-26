<?php

namespace OpenOrchestra\ElasticaBundle\Command;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraCreateIndexCommand
 */
class OrchestraCreateIndexCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:elastica:index:create')
            ->setDescription('Create an index in elastic search');
    }

    /**
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $this->getContainer()->getParameter('open_orchestra_elastica.index.name');
        $index = $this->getContainer()->get('open_orchestra_elastica.client.elastica')->getIndex($indexName);
        $index->create(
            array(
                'index' => array(
                    'number_of_shards' => 2,
                    'number_of_replicas' => 1
                )
            )
        );
    }
}

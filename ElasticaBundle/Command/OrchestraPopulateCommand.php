<?php

namespace OpenOrchestra\ElasticaBundle\Command;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraPopulateCommand
 */
class OrchestraPopulateCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:elastica:populate')
            ->setDescription('Populate the content index with the contents');
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
        foreach ($this->getContainer()->getParameter('open_orchestra_backoffice.orchestra_choice.front_language') as $language => $key) {
            $contents = $this
                ->getContainer()
                ->get('open_orchestra_model.repository.content')
                ->findByContentTypeAndKeywords($language);

            $this->getContainer()->get('open_orchestra_elastica.indexor.content')->indexMultiple($contents);
        }
    }
}

<?php

namespace AppBundle\Command;

use Bab\RabbitMq\Command\VhostMappingCreateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class SetupBrokerCommand extends VhostMappingCreateCommand implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('dunkerque:broker:setup')
            ->setDescription('Setup broker')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input = new ArrayInput(
            [
                'filepath' => 'app/Resources/broker/mapping.yml',
                '--vhost' => '/',
            ],
            new InputDefinition([
                new InputArgument('filepath'),
                new InputOption('vhost'),
                new InputOption('erase-vhost'),
            ])
        );

        parent::execute($input, $output);
    }

    protected function getCredentials(InputInterface $input, OutputInterface $output)
    {
        return [
            'host' => $this->container->getParameter('rabbitmq_host'),
            'port' => $this->container->getParameter('rabbitmq_management_port'),
            'user' => $this->container->getParameter('rabbitmq_login'),
            'password' => $this->container->getParameter('rabbitmq_password'),
        ];
    }
}

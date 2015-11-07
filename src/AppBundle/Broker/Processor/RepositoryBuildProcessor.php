<?php

namespace AppBundle\Broker\Processor;

use AppBundle\Entity\Manifest;
use AppBundle\Entity\Webhook;
use Doctrine\Common\Persistence\ObjectManager;
use Swarrot\Broker\Message;
use Swarrot\Processor\ProcessorInterface;

class RepositoryBuildProcessor implements ProcessorInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function process(Message $message, array $options)
    {
        $buildConfig = json_decode($message->getBody(), true);

        var_dump($buildConfig);
        echo "\n";
    }
}

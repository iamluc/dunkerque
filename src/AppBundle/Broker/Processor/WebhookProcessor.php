<?php

namespace AppBundle\Broker\Processor;

use AppBundle\Entity\Manifest;
use AppBundle\Entity\Webhook;
use Doctrine\Common\Persistence\ObjectManager;
use Swarrot\Broker\Message;
use Swarrot\Processor\ProcessorInterface;

class WebhookProcessor implements ProcessorInterface
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
        $data = json_decode($message->getBody(), true);

        $manifest = $this->manager->getRepository('AppBundle:Manifest')->find($data['id']);
        $webhooks = $this->manager->getRepository('AppBundle:Webhook')->findByRepository($manifest->getRepository());

        $payload = $this->getPayload($manifest);

        /** @var Webhook $webhook */
        foreach ($webhooks as $webhook) {

            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n",
                    'content' => $payload,
                    'timeout' => 2,
                    'ignore_errors' => true,
                ],
            ];
            $context = stream_context_create($options);
            $response = @file_get_contents($webhook->getUrl(), null, $context);
            if (isset($http_response_header) && is_array($http_response_header)) {
                $status = $http_response_header[0];
            } else {
                $status = 'Error';
            }
            unset($http_response_header);

            $webhook->setLastStatus($status);
            $webhook->setLastCall(new \DateTime());
        }

        $this->manager->flush();
    }

    private function getPayload(Manifest $manifest)
    {
        $repository = $manifest->getRepository();

        $data = [
            'repository' => [
                'repo_name' => $repository->getName(),
                'is_private' => $repository->isPrivate(),
                'star_count' => $repository->getStars(),
            ]
        ];

        return json_encode($data);
    }
}

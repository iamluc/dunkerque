<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RegistryExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => ['onKernelException'],
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $path = $event->getRequest()->getPathInfo();
        if ($path !== '/token' && 0 !== strpos($path, '/v2/')) {
            return;
        }

        $exception = $event->getException();
        $code = $exception instanceof HttpException ? $exception->getStatusCode() : $exception->getCode();
        $headers = $exception instanceof HttpException ? $exception->getHeaders() : [];
        $errorCode = isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : 'error';

        $response = new JsonResponse([
            'errors' => [
                [
                    'code' => strtoupper($errorCode),
                    'details' => null,
                    'message' => $exception->getMessage(),
                ],
            ],
        ], $code, $headers);

        $event->setResponse($response);
    }
}

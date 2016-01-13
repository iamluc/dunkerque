<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class RegistryEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $tokenEndpoint = $this->urlGenerator->generate('registry_token', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $serviceEndpoint = $this->urlGenerator->generate('index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $data = [
            'error' => [
                'code' => 'UNAUTHORIZED',
                'details' => null,
                'message' => 'access to the requested resource is not authorized',
            ],
        ];

        return new JsonResponse($data, 401, [
            'WWW-Authenticate' => sprintf('Bearer realm="%s",service="%s"', $tokenEndpoint, $serviceEndpoint),
        ]);
    }
}

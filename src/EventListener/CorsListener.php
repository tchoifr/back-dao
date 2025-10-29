<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CorsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 250],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getMethod() === 'OPTIONS') {
            $response = new \Symfony\Component\HttpFoundation\Response();
            $this->addCorsHeaders($response, $request);
            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        $this->addCorsHeaders($response, $request);
    }

    private function addCorsHeaders($response, $request): void
    {
        $origin = $request->headers->get('Origin');

        $allowedOrigins = [
            'https://workdao.netlify.app',
            'http://localhost:5173',
            'http://127.0.0.1:5173',
        ];

        if (in_array($origin, $allowedOrigins, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
        }
    }
}

<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CorsListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $origin = $request->headers->get('Origin');

        // 🔒 Autorise uniquement ces origines :
        $allowedOrigins = [
            'https://workdao.netlify.app',
            'http://localhost:5173',
        ];

        if (in_array($origin, $allowedOrigins, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        // 🧩 Gère aussi les requêtes préflight OPTIONS directement
        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(204);
            $event->setResponse($response);
        }
    }
}

<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// ✅ --- CORS FIX FOR KOYEB / NETLIFY ---
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $allowedOrigins = [
        'https://workdao.netlify.app',
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ];

    if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins, true)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    exit(0);
}
// ✅ --- END CORS FIX ---

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

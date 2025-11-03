<?php

use Symfony\Component\Dotenv\Dotenv;

// Ensure Composer autoload is available
require dirname(__DIR__).'/vendor/autoload.php';

// Load environment variables from .env if not already provided by the host
if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
    (new Dotenv())
        ->usePutenv()
        ->bootEnv(dirname(__DIR__).'/.env');
}


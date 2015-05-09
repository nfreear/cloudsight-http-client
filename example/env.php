<?php
/**
 * Load configuration for cloudsight-http-client examples.
 */
require_once __DIR__ . '/../vendor/autoload.php';

\Dotenv::load(__DIR__);
\Dotenv::required('CS_API_KEY');


if (getenv('CS_DEBUG')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
date_default_timezone_set('UTC');

# End.

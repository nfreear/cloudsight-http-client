<?php
/**
 * Load configuration for cloudsight-http-client examples.
 */
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from `.env`
try {
    \Dotenv::load(__DIR__);
    \Dotenv::required('CS_API_KEY');
} catch (Exception $ex) {
    if ('cli' == php_sapi_name()) {
        fwrite(
            STDERR,
            "Warning. Please fix & try again...\n\n> ". $ex->getMessage() .PHP_EOL
        );
    } else {
        header('HTTP/1.1 500.1');
        header('X-exception: '. $ex->getMessage());
        echo "<title>Error</title> Warning. Please fix & try again...<p>";
        echo $ex->getMessage();
    }
    exit(1);
}

if (getenv('CS_DEBUG')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
date_default_timezone_set('UTC');
define('CS_JSON', 'application/json; charset=utf-8');

# End.

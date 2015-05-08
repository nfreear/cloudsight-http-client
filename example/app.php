<?php
<<<CONFIG
packages:
    - "silex/silex: 1.2.*"
    - "nfreear/cloudsight-http-client: *"
php-options:
    - "-S"
    - "localhost:8000"
CONFIG;
/**
 * Cloudsight API test
 *
 * @author Nick Freear, 2 May 2015.
 * @copyright 2015 Nick Freear.
 *
 * @link  https://gist.github.com/nfreear/ac98e809b574948a47ab
 * @link  http://cloudsight.readme.io/v1.0/docs
 * @link  https://twitter.com/alt_text_bot/status/594605860388757504
 * @link  https://twitter.com/dartshipping/status/594605713298620416
 * @link  https://pbs.twimg.com/media/CEB2vgjUEAEfOB6.png
 *
 *  ~/.bashrc
 *  export COMPOSER_PROCESS_TIMEOUT=600
 */
require_once '../vendor/autoload.php';


define( 'CS_API_KEY_ENV', 'CLOUDSIGHT_API_KEY' );
define( 'CS_MOCK', true );
define( 'CS_JSON', 'application/json; charset=utf-8' );

date_default_timezone_set( 'Europe/London' );


use Nfreear\Cloudsight\Cloudsight_Http_Client;
use Symfony\Component\HttpFoundation\Request;



$app = new Silex\Application();
$app[ 'debug' ] = true;

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello ' . $app->escape($name) .' - '. date( 'H:m:i' );
});
$app->get('/cs{any}', function () use ($app) {
    if (!file_exists( __DIR__ .'/app-cs.html' )) {
        $app->abort( 404 );
    }
    return $app->sendFile( __DIR__ .'/app-cs.html' );
});
$app->get('/files/{path}', function ($path) use ($app) {
    if (!file_exists( __DIR__ .'/'. $path )) {
        $app->abort( 404 );
    }
    if (!preg_match( '/^[\w\-]+\.(html|js|css|png)$/', $path )) {
        $app->abort( 403 );  // Security!
    }
    return $app->sendFile( __DIR__ .'/'. $path );
});

$app->get('/api/image_requests', function (Request $request) use ($app) {

    $image_url = $request->query->get( 'image_url' );

    if (!getenv( CS_API_KEY_ENV )) {
        $error = array( 'message' => 'The required env var API_KEY is missing.' );
        return $app->json( $error, 400 );
    }
    if (!$image_url) {
        $error = array( 'message' => 'The required {image_url} parameter is missing.' );
        return $app->json( $error, 400, array( 'Content-Type' => CS_JSON ));
    }

    $post_data = array(
      'image_request[remote_image_url]' => $image_url,
      'image_request[locale]' => 'en-US',
      'image_request[language]' => 'en',
    );

    if ($app[ 'debug' ]) {
      header( 'X-cs-post-data: '. json_encode( $post_data ));
    }

    $client = new Cloudsight_Http_Client();
    $resp = $client->post_image_requests( $post_data );

    if ($app[ 'debug' ]) {
      $client->debug_headers();
    }

    return $app->json( $resp, $client->getStatus(), array( 'Content-Type' => CS_JSON ));
});
$app->get('/api/image_responses/{token}/{count}', function ($token, $count = 0) use ($app) {

    $client = new Cloudsight_Http_Client();
    $resp = $client->get_image_responses( $token, $count );

    return $app->json( $resp, $client->getStatus(), array( 'Content-Type' => CS_JSON ));
});

$app->run();


#End.

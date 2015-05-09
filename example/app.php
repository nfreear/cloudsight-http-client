<?php
<<<CONFIG
packages:
    - "silex/silex: 1.2.*"
    - "nfreear/cloudsight-http-client: dev-master"
php-options:
    - "-S"
    - "localhost:8000"
    - "-d"
    - "date.timezone=UTC"
CONFIG;
/**
 * Example Silex-based web server to test the Cloudsight API.
 *
 * @author Nick Freear, 2 May 2015.
 * @copyright 2015 Nick Freear.
 *
 * @link  https://gist.github.com/nfreear/ac98e809b574948a47ab
 * @link  https://twitter.com/alt_text_bot/status/594605860388757504
 * @link  https://twitter.com/dartshipping/status/594605713298620416
 *
 *  ~/.bashrc
 *  export COMPOSER_PROCESS_TIMEOUT=600
 */
require_once __DIR__ . '/env.php';

use Nfreear\Cloudsight\Cloudsight_Http_Client;


$app = new Silex\Application();
$app[ 'debug' ] = getenv('CS_DEBUG');


/** Sanity check.
 */
$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello ' . $app->escape($name) .' - '. date( 'H:m:i' );
});

/** HTML test page 'cs.html'
 *
 * @return object HTML response.
 */
$app->get('/cs{any}', function () use ($app) {
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


/** Use the image request endpoint to start the transaction.
 *
 * @return JsonResponse JSON, including a `token`.
 */
$app->get('/api/image_requests', function (Request $request) use ($app) {

    $api_key = getenv('CS_API_KEY');
    $query = $request->query;

    //$is_mock = $request->get( 'mock', true );
    $image_url = $query->get( 'image_url' );

    if (!$api_key) {
        $error = array( 'message' => 'The required env var API_KEY is missing.' );
        return $app->json( $error, 400 );
    }
    if (!$image_url) {
        $error = array( 'message' => 'The required {image_url} parameter is missing.' );
        return $app->json( $error, 400, array( 'Content-Type' => CS_JSON ));
    }

    $client = new Cloudsight_Http_Client($api_key, getenv('CS_MOCK'));

    $post_data = array(
      'remote_image_url' => $image_url,
      'locale' => $query->get( 'locale', 'en-US' ),
      'language' => $query->get( 'language', 'en' ),
    );

    $resp = $client->post_image_requests( $post_data );

    if ($app[ 'debug' ]) {
      $client->debug_headers();
    }

    return $app->json( $resp, $client->getStatus(), array( 'Content-Type' => CS_JSON ));
});


/** Poll the image response endpoint.
 *
 * @return JsonResponse JSON including the `status` ("not completed", "completed"...)
 */
$app->get('/api/image_responses/{token}/{count}', function ($token, $count = 0, Request $request) use ($app) {

    $api_key = getenv('CS_API_KEY');
    //$is_mock = $request->get( 'mock', true );

    $client = new Cloudsight_Http_Client( $api_key, getenv('CS_MOCK'));
    $resp = $client->get_image_responses( $token, $count );

    return $app->json( $resp, $client->getStatus(), array( 'Content-Type' => CS_JSON ));
});

$app->run();

#End.

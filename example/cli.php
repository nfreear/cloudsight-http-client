<?php
/**
 * Example commandline script to demonstrate the Cloudsight API.
 *
 * @copyright 2015 Nick Freear.
 */
require_once __DIR__ . '/env.php';

use Nfreear\Cloudsight\Cloudsight_Http_Client;

// Create an instance of the client.
$client = new CloudSight_Http_Client(getenv('CS_API_KEY'), getenv('CS_MOCK'));

// Call once.
$request = $client->postImageRequests(getenv('EXAMPLE_IMAGE_URL'));

if (getenv('CS_DEBUG')) {
    var_dump($client->getPostData(), $request);
}

$count  = 0;
while (1) {

    sleep( 1 );

    // Poll the API, with the token.
    $result = $client->getImageResponses($request->token, $count);

    echo "$count. Status: " . $result->status . PHP_EOL;

    // Check if image analysis is complete.
    if ($client->isComplete()) {
        break;
    }
    $count++;
}

if (getenv('CS_DEBUG')) {
    var_dump($result);
}

echo "ALT text: " . $result->name . PHP_EOL;
echo "End.\n";

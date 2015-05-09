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
$request = $client->post_image_requests(getenv('EXAMPLE_IMAGE_URL'));

if (getenv('CS_DEBUG')) {
    var_dump($client->get_post_data(), $request);
}

$count  = 0;
while (1) {

    sleep( 1 );

    // Poll the API, with the token.
    $result = $client->get_image_responses($request->token, $count);

    echo "$count. Status: " . $result->status . PHP_EOL;

    // Check if image analysis is complete.
    if ($client->is_complete()) {
        break;
    }
    $count++;
}

if (getenv('CS_DEBUG')) {
    var_dump($result);
}

echo "ALT text: " . $result->name . PHP_EOL;
echo "End.\n";

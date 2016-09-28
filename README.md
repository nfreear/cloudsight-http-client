[![Latest Stable Version][packagist-icon]][packagist]
[![MIT License][license-icon]][MIT]
[![Build status: Travis-CI][travis-icon]][travis-ci]

# cloudsight-http-client

PHP HTTP client library for the [CloudSight][] image recognition API, with mock API functionality.


## Installation

Install using [Composer][], via [Packagist][]. At the commandline / in a terminal, type:


    composer require nfreear/cloudsight-http-client:dev-master


## Usage

1. Register with [CloudSight][] to get an API key.

2. At the commandline / in a terminal, type:
  ```
  composer copy-env
  ```

3. Edit the `CS_API_KEY` variable, in the `example/.env` configuration file, using your favourite text editor:
  ```
  atom example/.env
  ```

4. Try the command line example:
  ```
  composer example
  ```

5. And, a web server based example:
  ```
  composer web
  ```

Note: you'll want to set the `CS_MOCK` variable to `false`, to run _live_ demos!


## Legacy

```php
<?php
use Nfreear\Cloudsight\Cloudsight_Http_Client;

$client = new CloudSight_Http_Client($api_key);

$request = $client->postImageRequests($image_url);

while (1) {

    sleep(1);

    $result = $client->getImageResponses($request->token);

    // Check if analysis is complete.
    if ($client->isComplete()) {
        break;
    }
}

echo "Complete. ALT text: " . $result->name;
?>
```


---
_NOTE: this library is NOT endorsed by CloudSight._


© 2016 Nick Freear. License: [MIT][].

[![author: @nfreear on Twitter][author-icon]][twitter]


[github]: https://github.com/nfreear/cloudsight-http-client
[source-icon]: https://img.shields.io/badge/source-nfreear%2Fcloudsight--http--client-blue.svg
[travis-icon]: https://travis-ci.org/nfreear/cloudsight-http-client.svg
[travis-ci]: https://travis-ci.org/nfreear/cloudsight-http-client
[twitter]: https://twitter.com/nfreear
[author-icon]: https://img.shields.io/badge/author-%40nfreear-blue.svg
[Packagist]: https://packagist.org/packages/nfreear/cloudsight-http-client
[packagist-icon]: https://img.shields.io/packagist/v/nfreear/cloudsight-http-client.svg
[license-icon]: https://img.shields.io/packagist/l/nfreear/cloudsight-http-client.svg
[MIT]: http://nfreear.mit-license.org/
[CloudSight]: https://cloudsightapi.com/
[CloudSight documentation]: http://cloudsight.readme.io/v1.0/docs
[Composer]: https://getcomposer.org/

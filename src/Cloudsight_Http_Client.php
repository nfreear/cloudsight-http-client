<?php
/**
 * HTTP client for the CloudSight API, with mock API functionality.
 *
 * (This library is NOT endorsed by CloudSight.)
 *
 * @author Nick Freear, 2 May 2015.
 * @copyright 2015 Nick Freear.
 *
 * @link  http://cloudsight.readme.io/v1.0/docs
 * @link  https://gist.github.com/nfreear/ac98e809b574948a47ab
 */
namespace Nfreear\Cloudsight;

define('CS_API_KEY_ENV', 'CLOUDSIGHT_API_KEY');


class Cloudsight_Http_Client extends \Net_Http_Client
{

    const BASE = 'https://api.cloudsightapi.com/';

    public function __construct()
    {
        parent::__construct();
        $this->setHeader('Authorization', 'CloudSight '. getenv(CS_API_KEY_ENV));
        $this->setHeader('Accept', 'application/json');
        $this->setUserAgent('Nicks-test-1/PHP; nfreear+@y+ahoo.co.uk');
    }

    public function post_image_requests($post_data)
    {
        if ($this->is_mock()) {
            return array(
            'mock' => true,
            'url' => "//images.cloudsightapi.com/uploads/image_request/__/Image.jpg",
            'token' => 'Mock_AJKAWHKGLjqMd9KDNIXQfg',
            );
        }
        #$this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->setHeader('Content-Type', 'multipart/form-data');
        $this->post(self::BASE . 'image_requests', $post_data);
        return $this->parse_json();
    }

    public function get_image_responses($token, $mock_count = 0)
    {
        if ($this->is_mock()) {
            return $this->mock_image_responses($mock_count);
        }
        $this->get(sprintf(self::BASE . 'image_responses/%s', $token));
        return $this->parse_json();
    }

    public function debug_headers()
    {
        if ($this->is_mock()) {
            return;
        }
        header('X-cs-api-key: ' . json_encode(getenv(CS_API_KEY_ENV)));
        header('X-cs-headers: '. json_encode($this->getHeaders()));
        header('X-cs-info: '. json_encode($this->getInfo()));
    }

    public function parse_json()
    {
        return 200 == $this->getStatus() ? json_decode($this->getBody()) : null;
    }

    public function getStatus()
    {
        return $this->is_mock() ? 200 : parent::getStatus();
    }

    protected function is_mock()
    {
        return CS_MOCK;
    }

    protected function mock_image_responses($count)
    {
        $count = intval($count);
        $rand = mt_rand(2, 7);
        $mock_resp = array(
            'mock' => true,
            'mock_rand' => $rand,
            'mock_count' => $count,
            'status' => 'not completed',
        );
        if ($count < $rand) {
            return $mock_resp;
        }
        $mock_resp[ 'status' ] = 'completed';
        $mock_resp[ 'name' ] = 'red beats by dre headphones';
        return $mock_resp;
    }
}

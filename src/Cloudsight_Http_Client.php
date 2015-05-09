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

class Cloudsight_Http_Client extends \Net_Http_Client
{
    private $api_key;
    protected $mock;

    const BASE = 'https://api.cloudsightapi.com/';

    public function __construct($api_key = null, $mock = null)
    {
        parent::__construct();

        $this->api_key = $api_key;
        $this->mock = (bool)$mock;

        $this->setHeader('Authorization', 'CloudSight '. $this->api_key);
        $this->setHeader('Accept', 'application/json');
    }

    public function post_image_requests($post_data)
    {
        if ($this->is_mock()) {
            return $this->mock_image_requests();
        }
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
        header('X-cs-api-key: ' . json_encode($this->api_key));
        header('X-cs-headers: '. json_encode($this->getHeaders()));
        header('X-cs-info: '. json_encode($this->getInfo()));
    }

    protected function parse_json()
    {
        return 200 == $this->getStatus() ? json_decode($this->getBody()) : null;
    }

    public function post_data($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            $result[ \sprintf('image_request[%s]', $key) ] = $value;
        }
        return $result;
    }

    public function getStatus()
    {
        return $this->is_mock() ? 200 : parent::getStatus();
    }


    // ==================================================

    protected function is_mock()
    {
        return $this->mock;
    }

    protected function mock_image_requests() {
        return array(
            'mock' => true,
            'url' => "//images.cloudsightapi.com/uploads/image_request/__/Image.jpg",
            'token' => 'Mock_AJKAWHKGLjqMd9KDNIXQfg',
        );
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

<?php
namespace StoreConnect\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\TransferStats;

class StoreConnectAPI {
    
    public $is_enable;

    public $base_url = 'https://example.com';

    public function __construct() {

        $this->base_url = get_option('wc_settings_tab_storeconnect_base_url');
        $this->is_enable = get_option('wc_settings_tab_storeconnect_is_enable');

        // if ( $this->is_enable ) {
            
        // }
    }

    private function client() {

        return new Client([
            'base_uri' => $this->base_url . '/v1/',
            'timeout'  => 100,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }


    public function get($url, array $params = []) {
        try {
            $response = $this->client()->get($url, [
                // 'auth'      => [ 0, $this->is_enable ],
                'query'     => $params,
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ]);

            return $response->getBody();
        } catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            error_log('====================API ERROR==================');
            error_log('URL: ' . $url);
            error_log((string) $response->getBody());
            die();
            var_dump((string) $response->getBody());
        }
    }

    public function post($url, $formData) {
        try {
            $response = $this->client()->post($url, [
                // 'auth'      => [ 0, $this->is_enable ],
                'body'     => $formData,
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ]);

            return $response->getBody();
        } catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            error_log('====================API ERROR==================');
            error_log('URL: ' . $url);
            error_log((string) $response->getBody());
            die();
            var_dump((string) $response->getBody());
        }
    }

    public function put($url, $formData) {

        try {
            $response = $this->client()->put($url, [
                'auth'      => [ 0, $this->is_enable ],
                // 'json'     => $formData,
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ]);

            return $response->getBody();
        } catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            error_log('====================API ERROR==================');
            error_log('URL: ' . $url);
            error_log((string) $response->getBody());
            var_dump((string) $response->getBody());
            die();

        }
    }
}
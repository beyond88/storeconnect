<?php

namespace StoreConnect\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\TransferStats;

/**
 * Handles communication with the StoreConnect API.
 */
class StoreConnectAPI
{

    /**
     * Indicates whether the StoreConnect API is enabled.
     *
     * @var string
     */
    public $is_enable;

    /**
     * The base URL of the StoreConnect API.
     *
     * @var string
     */
    public $base_url = 'https://example.com';

    /**
     * Initializes a new instance of the StoreConnectAPI class.
     */
    public function __construct()
    {
        $this->base_url = get_option('wc_settings_tab_storeconnect_base_url');
        $this->is_enable = get_option('wc_settings_tab_storeconnect_is_enable');
    }

    /**
     * Checks if the StoreConnect API is enabled.
     *
     * @return bool True if the API is enabled, otherwise false.
     */
    public function is_enabled()
    {
        return get_option('wc_settings_tab_storeconnect_is_enable');
    }

    /**
     * Creates a Guzzle HTTP client instance.
     *
     * @return Client The Guzzle HTTP client instance.
     */
    private function client()
    {

        return new Client([
            'base_uri' => $this->base_url . '/v1/',
            'timeout'  => 100,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Sends a GET request to the specified URL with the provided parameters.
     *
     * @param string $url The URL to send the GET request to.
     * @param array $params The parameters to include in the request.
     * @return mixed The response body of the GET request.
     */
    public function get($url, array $params = [])
    {

        if ($this->is_enabled() === 'no') {
            error_log('StoreConnect API is disabled.');
            return;
        }

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

    /**
     * Sends a POST request to the specified URL with the provided form data.
     *
     * @param string $url The URL to send the POST request to.
     * @param array $formData The form data to include in the request.
     * @return mixed The response body of the POST request.
     */
    public function post($url, $formData)
    {

        if ($this->is_enabled() === 'no') {
            error_log('StoreConnect API is disabled.');
            return;
        }

        try {
            $response = $this->client()->post($url, [
                // 'auth'      => [ 0, $this->is_enable ],
                'form_params'     => $formData,
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ]);

            return $response;
        } catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            error_log('====================API ERROR==================');
            error_log('URL: ' . $url);
            error_log((string) $response->getBody());
            die();
            var_dump((string) $response->getBody());
        }
    }

    /**
     * Sends a PUT request to the specified URL with the provided form data.
     *
     * @param string $url The URL to send the PUT request to.
     * @param array $formData The form data to include in the request.
     * @return mixed The response body of the PUT request.
     */
    public function put($url, $formData)
    {

        if ($this->is_enabled() === 'no') {
            error_log('StoreConnect API is disabled.');
            return;
        }

        try {
            $response = $this->client()->put($url, [
                // 'auth'      => [ 0, $this->is_enable ],
                'form_params' => $formData,
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ]);

            return $response;
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

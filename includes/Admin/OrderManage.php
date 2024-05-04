<?php

namespace StoreConnect\Admin;

use StoreConnect\API\StoreConnectAPI;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use StoreConnect\Traits\Singleton;


class OrderManage
{

    use Singleton;

    private $api;

    /**
     * Bootstrap all methods for order
     * 
     * @since  	1.0.0
     * @access	public
     * @param none
     * @return void
     */
    public function init()
    {
        $this->api = new StoreConnectAPI();
        add_action('before_delete_post', array($this, 'delete_order_hook'));
    }

    /**
     * Hook callback to detect order deletions
     * 
     * @since  	1.0.0
     * @access	public
     * @param 	int $post_id The ID of the post being deleted
     * @return 	void
     */
    public function delete_order_hook($order_id)
    {

        if (get_post_type($order_id) === 'shop_order') {
            $order = wc_get_order($order_id);
            if ($order) {
                $data = array(
                    'order_id' => get_post_meta($order_id, '_hub_item_id', true),
                );
                $response = $this->api->post('order/delete', $data);

                if ($response instanceof Response) {
                    // Get the response status code
                    $status_code = $response->getStatusCode();
                    // Get the response body
                    $response_body = (string) $response->getBody();

                    $json_response = json_decode($response_body, true);

                    // Log successful data transmission
                    error_log('Order data is deleted successfully. Status Code: ' . $status_code . ', Response Body: ' . $response_body);
                } else {
                    // Handle unexpected response type
                    error_log('Unexpected response type: ');
                }
            }
        }
    }
}

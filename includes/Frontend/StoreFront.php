<?php

namespace StoreConnect\Frontend;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use StoreConnect\API\StoreConnectAPI;
use StoreConnect\API\Resources\Order;

/**
 * Ajax handler class
 */
class StoreFront
{

    private $api;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->api = new StoreConnectAPI();
        add_action('woocommerce_order_status_changed', array($this, 'send_order_data_to_hub'), PHP_INT_MAX, 3);
    }

    /**
     * Sends order data to Hub when the order status is changed.
     *
     * @param int $order_id The ID of the order.
     * @param string $old_status The old order status.
     * @param string $new_status The new order status.
     */
    public function send_order_data_to_hub($order_id, $old_status, $new_status)
    {

        $hub_item_id = get_post_meta($order_id, '_hub_item_id', true);
        if (empty($hub_item_id)) {
            $order = new Order();
            $order_data = $order->get_order($order_id, $new_status);
            $order->send_data_to_hub($order_data);
        }
    }


}

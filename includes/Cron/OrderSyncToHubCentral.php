<?php

namespace StoreConnect\Cron;

use StoreConnect\API\Resources\Order;
use StoreConnect\Traits\Singleton;
use StoreConnect\Helper;
use StoreConnect\API\StoreConnectAPI;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;

class OrderSyncToHubCentral
{
    use Singleton;

    public $manager;
    public $recordManager;

    public $orderLimit = 10; // 10 Order will be sync per minute
    public $paginateOptionName = 'storeconnect_orders_sync_pagination';

    public function __construct()
    {
    }

    /**
     * @throws \Exception
     * TODO:: use single event
     */
    public function sync()
    {

        $batch_size = 10;
        $cache_key = 'stc_order_sync_cache';

        $synced_order_ids = wp_cache_get($cache_key, array());

        if (empty($synced_order_ids)) {
            $unsynced_orders = $this->get_unsynced_orders();
            $synced_order_ids = array_column($unsynced_orders, 'ID');
            error_log('total order found without column: ' . count($synced_order_ids));
            wp_cache_set($cache_key, $synced_order_ids, HOUR_IN_SECONDS);
        }

        $total_orders = count($synced_order_ids);
        $order = new Order();

        for ($i = 0; $i < $batch_size && $i < $total_orders; $i++) {
            $order_id = $synced_order_ids[$i];
            $order_data = $order->get_order($order_id);
            $success = $order->send_data_to_hub($order_data);

            if ($success) {
                error_log("Order data sent to Hub successfully. Order ID: $order_id");
            } else {
                error_log("Failed to send order data (ID: $order_id) to hub.");
            }
        }
    }

    // Helper function to fetch unsynced orders (replace with your actual logic)
    private function get_unsynced_orders()
    {

        global $wpdb;
        $orders_table = $wpdb->prefix . 'posts';
        $meta_table = $wpdb->prefix . 'postmeta';
        $post_type = 'shop_order';
        $meta_key = '_hub_item_id';

        $query = $wpdb->prepare("
            SELECT p.ID
            FROM $orders_table p
            LEFT JOIN $meta_table pm ON p.ID = pm.post_id AND pm.meta_key = '" . $meta_key . "'
            WHERE pm.meta_id IS NULL
            AND p.post_type = %s
            ORDER BY p.ID DESC
        ", $post_type);

        $orders = $wpdb->get_results($query, ARRAY_A);
        error_log('total order found: ', count($orders));
        return $orders ? $orders : array();
    }
}

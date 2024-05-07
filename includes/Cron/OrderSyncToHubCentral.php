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

        // Retrieve data from transient
        $synced_order_ids = get_transient($cache_key);

        if (!$synced_order_ids) {
            // If transient doesn't exist or has expired, regenerate data
            $unsynced_orders = $this->get_unsynced_orders();
            $synced_order_ids = array_column($unsynced_orders, 'ID');
            error_log('total order found by query: ' . count($synced_order_ids));

            // Set transient with expiration time of 1 hour
            set_transient($cache_key, $synced_order_ids, HOUR_IN_SECONDS);
        } else {
            error_log('total order found by transient: ' . count($synced_order_ids));
        }

        //$total_orders = count($synced_order_ids);
        $order = new Order();

        $processed_orders = []; // Keep track of processed orders for removal

        foreach ($synced_order_ids as $i => $order_id) {
            if (count($processed_orders) >= $batch_size) {
                break; // Stop processing after reaching batch size
            }

            $order_data = $order->get_order($order_id);
            $success = $order->send_data_to_hub($order_data);

            if ($success) {
                $processed_orders[] = $order_id; // Track processed orders
                error_log("Order data sent to Hub successfully. Order ID: $order_id");
            } else {
                error_log("Failed to send order data (ID: $order_id) to hub.");
            }
        }

        // Update transient with remaining orders (if any)
        $remaining_orders = array_diff($synced_order_ids, $processed_orders);
        if (!empty($remaining_orders)) {
            set_transient($cache_key, $remaining_orders, HOUR_IN_SECONDS);
        }

        return ['processed_orders' => count($processed_orders), 'remaining_orders' => count($remaining_orders)];
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
        return $orders ? $orders : array();
    }
}

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

        // $args = array(
        //     'post_type' => 'shop_order',
        //     'posts_per_page' => 10,
        //     'fields'         => 'ids',
        //     'meta_query' => array(
        //         array(
        //             'key' => '_hub_item_id',
        //             'compare' => 'NOT EXISTS',
        //         ),
        //     ),
        // );

        // $query = new \WP_Query($args);
        // if ($query->have_posts()) {
        //     while ($query->have_posts()) {
        //         $query->the_post();
        //         $order_id = get_the_ID();
        //         $order = new Order();
        //         $order_data = $order->get_order($order_id);
        //         $order->send_data_to_hub($order_data);
        //     }

        //     wp_reset_postdata();
        // } else {
        //     echo 'No orders found matching the criteria.';
        // }


        $batch_size = 10;
        $max_iterations = 10;
        $cache_key = 'stc_order_sync_cache';

        // **Check for Cached IDs:**
        $synced_order_ids = wp_cache_get($cache_key, false);

        if (false === $synced_order_ids) {
            $args = array(
                'post_type' => 'shop_order',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => '_hub_item_id',
                        'compare' => 'NOT EXISTS',
                    ),
                ),
            );

            $query = new \WP_Query($args);

            if ($query->have_posts()) {
                $synced_order_ids = $query->get_posts();
            } else {
                $synced_order_ids = array();
            }

            wp_cache_set($cache_key, $synced_order_ids, HOUR_IN_SECONDS);
        }

        $processed_orders = 0;
        do {
            $iteration = 0;

            // **Process Orders from Cache:**
            $order_ids_to_process = array_slice($synced_order_ids, $processed_orders, $batch_size);

            if (empty($order_ids_to_process)) {
                break; // Exit loop if no more orders to process
            }

            foreach ($order_ids_to_process as $order_id) {

                $order = new Order();
                $order_data = $order->get_order($order_id);
                $success = $order->send_data_to_hub($order_data);

                if ($success) {
                    $processed_orders++;

                    $synced_order_ids = array_slice($synced_order_ids, $processed_orders);
                    wp_cache_set($cache_key, $synced_order_ids, HOUR_IN_SECONDS);
                } else {
                    error_log("Failed to send order data (ID: $order_id) to hub.");
                }

                $iteration++;
                if ($iteration >= $batch_size) {
                    break;
                }
            }

            wp_reset_postdata();

            // **Deadlock Handling:** Check for successful processing and limit iterations
        } while ($processed_orders > 0 && $iteration < $max_iterations);

        if ($iteration >= $max_iterations) {
            error_log("Potential deadlock encountered during order sync. Consider increasing max_iterations or investigating database issues.");
        }
    }
}

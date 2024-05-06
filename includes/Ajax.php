<?php

namespace StoreConnect;

use StoreConnect\Cron\OrderSyncToHubCentral;

/**
 * Ajax handler class
 */
class Ajax
{

    /**
     * Constructor method for Ajax class.
     * Adds action hooks for AJAX endpoints.
     */
    function __construct()
    {
        add_action('wp_ajax_sync_order', array($this, 'sync_order'));
        add_action('wp_ajax_nopriv_sync_order', array($this, 'sync_order'));

        add_action('wp_ajax_stop_sync_order', array($this, 'stop_sync_order'));
        add_action('wp_ajax_nopriv_stop_sync_order', array($this, 'stop_sync_order'));

        add_action('wp_ajax_sync_status', array($this, 'sync_status'));
        add_action('wp_ajax_nopriv_sync_status', array($this, 'sync_status'));
    }

    /**
     * AJAX callback function for syncing orders.
     *
     * @return void
     */
    public function sync_order()
    {
        check_ajax_referer('storeconnect-admin-nonce', 'security');

        if (!empty($_POST)) {
            if (!wp_next_scheduled('storeconnect_sync_orders_schedule')) {
                wp_schedule_event(time(), 'storeconnect_sync_1_min', 'storeconnect_sync_orders_schedule');
            }

            OrderSyncToHubCentral::instance()->sync();

            wp_send_json_success(
                "<p class='storeconnect_success'>" . __('Order sync in progress', 'storeconnect') . "</p>",
                200
            );
        } else {
            wp_send_json_error(
                array(
                    __('Something went wrong', 'storeconnect')
                ),
                200
            );
        }

        wp_die();
    }

    /**
     * AJAX callback function for fetching synchronization status.
     *
     * @return void
     */
    public function sync_status()
    {
        check_ajax_referer('storeconnect-admin-nonce', 'security');

        if (!empty($_POST)) {
            $sync_status = get_option('storeconnect_orders_sync_pagination');
            $schedule = wp_get_schedule('storeconnect_sync_orders_schedule');
            if ($sync_status || $schedule) {
                wp_send_json_success(
                    ['data' => $sync_status, 'selector' => '#sync_order_message'],
                    200
                );
            }

            wp_send_json_success(['data' => false], 200);
        } else {
            wp_send_json_error(
                array(
                    __('Something went wrong', 'storeconnect')
                ),
                402
            );
        }
    }

    /**
     * AJAX callback function for stopping order synchronization.
     *
     * @return void
     */
    public function stop_sync_order()
    {
        check_ajax_referer('storeconnect-admin-nonce', 'security');

        delete_option('storeconnect_orders_sync_pagination');
        wp_clear_scheduled_hook('storeconnect_sync_orders_schedule');

        wp_send_json_success(
            "",
            200
        );
    }
}

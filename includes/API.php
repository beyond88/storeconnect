<?php

namespace StoreConnect;

use StoreConnect\API\Resources\Order;

/**
 * API Class
 */
class API
{

    /**
     * Initialize the class
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    function __construct()
    {
        add_action('rest_api_init', array($this, 'register_api'));
    }

    /**
     * Register the API
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    public function register_api()
    {

        $order = new Order();

        register_rest_route('storeconnect/v1', '/hub-update', array(
            'methods' => 'POST',
            'callback' => array($order, 'process_order_request_from_hub'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('storeconnect/v1', '/sync-start', array(
            'methods' => 'POST',
            'callback' => array($order, 'sync_start'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('storeconnect/v1', '/sync-status', array(
            'methods' => 'POST',
            'callback' => array($order, 'sync_status'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('storeconnect/v1', '/sync-stop', array(
            'methods' => 'POST',
            'callback' => array($order, 'stop_sync'),
            'permission_callback' => '__return_true',
        ));
    }
}

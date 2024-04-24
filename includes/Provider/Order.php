<?php
namespace StoreConnect\Provider;

class Order {

    public function __construct() {}

    /**
     * Handle POST request from HubCentral.
     *
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response The REST response object.
     */
    public function handle_hub_update_request(WP_REST_Request $request) {
        $data = $request->get_json_params();

        if (!$data) {
            return new WP_REST_Response(array('error' => 'Invalid data'), 400);
        }

        // Process the received data and update WooCommerce orders
        $this->process_hub_update_data($data);

        return new WP_REST_Response(array('success' => true), 200);
    }

    /**
     * Process the received data and update WooCommerce orders.
     *
     * @param array $data The data received from HubCentral.
     */
    public function process_hub_update_data($data) {
        foreach ($data as $order_data) {
            $order_id = $order_data['order_id'];
            $status = $order_data['status'];
            $notes = isset($order_data['notes']) ? $order_data['notes'] : '';

            $order = wc_get_order($order_id);

            if ($order) {
                if ($status && $status !== $order->get_status()) {
                    $order->update_status($status);
                }

                if ($notes) {
                    $order->add_order_note($notes);
                }
            }
        }
    }

    
}
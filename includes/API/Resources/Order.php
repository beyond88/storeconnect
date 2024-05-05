<?php

namespace StoreConnect\API\Resources;

use WP_REST_Request;
use WP_REST_Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Response;
use StoreConnect\API\StoreConnectAPI;

class Order
{

    /**
     * StoreConnectAPI instance.
     *
     * @var StoreConnectAPI
     */
    private $api;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->api = new StoreConnectAPI();
    }

    /**
     * Retrieves order data based on order ID and optionally specified status.
     *
     * @param int $order_id The ID of the order.
     * @param string|null $status Optional. The status of the order. Defaults to NULL.
     * @return array An array containing order data.
     */
    public function get_order($order_id, $status = NULL)
    {

        $order = wc_get_order($order_id);

        if (empty($status)) {
            $status = $order->get_status();
        }

        $order_data = array(
            'order_id' => $order->get_id(),
            'status' => $status,
            'order_total' => $order->get_total(),
            'billing_email' => $order->get_billing_email(),
            'billing_phone' => $order->get_billing_phone(),
            'shipping_first_name' => $order->get_shipping_first_name(),
            'shipping_last_name' => $order->get_shipping_last_name(),
            'shipping_company' => $order->get_shipping_company(),
            'shipping_address_1' => $order->get_shipping_address_1(),
            'shipping_address_2' => $order->get_shipping_address_2(),
            'shipping_city' => $order->get_shipping_city(),
            'shipping_state' => $order->get_shipping_state(),
            'shipping_postcode' => $order->get_shipping_postcode(),
            'shipping_country' => $order->get_shipping_country(),
            'billing_first_name' => $order->get_billing_first_name(),
            'billing_last_name' => $order->get_billing_last_name(),
            'billing_company' => $order->get_billing_company(),
            'billing_address_1' => $order->get_billing_address_1(),
            'billing_address_2' => $order->get_billing_address_2(),
            'billing_city' => $order->get_billing_city(),
            'billing_state' => $order->get_billing_state(),
            'billing_postcode' => $order->get_billing_postcode(),
            'billing_country' => $order->get_billing_country(),
            'payment_method' => $order->get_payment_method(),
            'payment_method_title' => method_exists($order, 'get_payment_method_title') ? $order->get_payment_method_title() : '',
            'shipping_method' => method_exists($order, 'get_shipping_method') ? $order->get_shipping_method() : '',
            'shipping_total' => $order->get_shipping_total(),
            'cart_tax' => $order->get_cart_tax(),
            'shipping_tax' => $order->get_shipping_tax(),
            'total_tax' => $order->get_total_tax(),
            'customer_id' => $order->get_customer_id(),
            'customer_ip_address' => $order->get_customer_ip_address(),
            'customer_user_agent' => $order->get_customer_user_agent(),
            'customer_note' => $order->get_customer_note(),
            'date_created' => $order->get_date_created() ? $order->get_date_created()->format('Y-m-d H:i:s') : '',
            'date_modified' => $order->get_date_modified() ? $order->get_date_modified()->format('Y-m-d H:i:s') : '',
            'date_completed' => $order->get_date_completed() ? $order->get_date_completed()->format('Y-m-d H:i:s') : '',
            'cart_hash' => $order->get_cart_hash(),
            'order_key' => $order->get_order_key(),
            'coupon_lines' => method_exists($order, 'get_coupon_lines') ? $order->get_coupon_lines() : '',
            'currency' => $order->get_currency(),
            'discount_total' => $order->get_discount_total(),
            'discount_tax' => $order->get_discount_tax(),
            'shipping_method_title' => method_exists($order, 'get_shipping_method_title') ? $order->get_shipping_method_title() : '',
            'shipping_method_id' => method_exists($order, 'get_shipping_method_id') ? $order->get_shipping_method_id() : '',
            'refunds' => $order->get_refunds(),
        );

        $order_notes = wc_get_order_notes(array(
            'order_id' => $order->get_id(),
        ));

        foreach ($order_notes as $note) {
            $order_data['order_notes'][] = array(
                'note_id' => $note->id,
                'note_author' => $note->added_by,
                'note_date' => $note->date_created->format('Y-m-d H:i:s'),
                'note_content' => $note->content,
            );
        }

        $order_meta_data = $order->get_meta_data();
        foreach ($order_meta_data as $meta) {
            $meta_key = $meta->key;
            $meta_value = $meta->value;

            $order_data['meta_data'][$meta_key] = $meta_value;
        }

        return $order_data;
    }

    /**
     * Sends order data to Hub using HTTP requests.
     *
     * @param array $data The order data to send to Hub.
     */
    public function send_data_to_hub($data)
    {
        try {
            // Make API request using GuzzleHTTP
            $response = $this->api->post('order', $data);

            if ($response instanceof Response) {
                $status_code = $response->getStatusCode();
                $response_body = (string) $response->getBody();

                $json_response = json_decode($response_body, true);
                if (isset($json_response['post_id'])) {
                    $post_id = $json_response['post_id'];
                    update_post_meta($data['order_id'], '_hub_item_id', $post_id);
                }
                error_log('Order data sent to Hub successfully. Status Code: ' . $status_code . '');
            } else {
                error_log('Unexpected response type: ');
            }
        } catch (RequestException $e) {
            // Handle request exception
            error_log('Error sending order data to Hub: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Handle POST request from HubCentral.
     *
     * @param WP_REST_Request $request The REST request object.
     * @return WP_REST_Response The REST response object.
     */
    public function process_order_request_from_hub(WP_REST_Request $request)
    {
        $data = $request->get_json_params();

        if (!$data) {
            return new WP_REST_Response(array('error' => 'Invalid data'), 400);
        }

        $this->update_order_information_from_hub($data);

        return new WP_REST_Response(array('success' => true), 200);
    }

    /**
     * Process the received data and update WooCommerce orders.
     *
     * @param array $data The data received from HubCentral.
     */
    private function update_order_information_from_hub($data)
    {
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

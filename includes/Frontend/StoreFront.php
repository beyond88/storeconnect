<?php
namespace StoreConnect\Frontend;
use StoreConnect\API\StoreConnectAPI;

/**
 * Ajax handler class
 */
class StoreFront {

    private $api;

    /**
     * Class constructor
     */
    public function __construct() {

        $this->api = new StoreConnectAPI();

        add_action( 'woocommerce_checkout_order_processed', array( $this, 'send_order_data_to_hub') , 10, 3 );
    }

    /**
     * Sends order data to Hub immediately after an order is placed.
     *
     * @param int $order_id The ID of the created order.
     * @param array $posted_data Array of posted data from the checkout form.
     * @param WC_Order $order The newly created order object.
     */
    public function send_order_data_to_hub( $order_id, $posted_data, $order ) {
        
        // Check if the order exists
        if ( ! $order ) {
            return;
        }

        // Prepare order data to send to Hub
        $order_data = array(
            'order_id' => $order->get_id(),
            'status' => $order->get_status(),
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
            'payment_method_title' => $order->get_payment_method_title(),
            'shipping_method' => $order->get_shipping_method(),
            'shipping_total' => $order->get_shipping_total(),
            'cart_tax' => $order->get_cart_tax(),
            'shipping_tax' => $order->get_shipping_tax(),
            'total_tax' => $order->get_total_tax(),
            'customer_id' => $order->get_customer_id(),
            'customer_ip_address' => $order->get_customer_ip_address(),
            'customer_user_agent' => $order->get_customer_user_agent(),
            'date_created' => $order->get_date_created() ? $order->get_date_created()->format('Y-m-d H:i:s') : '',
            'date_modified' => $order->get_date_modified() ? $order->get_date_modified()->format('Y-m-d H:i:s') : '',
            'date_completed' => $order->get_date_completed() ? $order->get_date_completed()->format('Y-m-d H:i:s') : '',
            'cart_hash' => $order->get_cart_hash(),
            'order_key' => $order->get_order_key(),
            'coupon_lines' => $order->get_coupon_lines(),
            'currency' => $order->get_currency(),
            'discount_total' => $order->get_discount_total(),
            'discount_tax' => $order->get_discount_tax(),
            'shipping_method_title' => $order->get_shipping_method_title(),
            'shipping_method_id' => $order->get_shipping_method_id(),
            'refunds' => $order->get_refunds(),
        );

        // Get order notes
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
    
        // Get order meta data
        $order_meta_data = $order->get_meta_data();
    
        // Add order meta data to order data
        foreach ($order_meta_data as $meta) {
            $meta_key = $meta->key;
            $meta_value = $meta->value;
    
            $order_data['meta_data'][$meta_key] = $meta_value;
        }

        // Send order data to Hub
        $this->send_data_to_hub( $order_data );
    }

    /**
     * Sends order data to Hub using HTTP requests.
     *
     * @param array $data The order data to send to Hub.
     */
    public function send_data_to_hub( $data ) {

        $response = $this->api->post('orders', json_encode($data) );

        // Check for errors
        if ( is_wp_error( $response ) ) {
            error_log( 'Error sending order data to Hub: ' . $response->get_error_message() );
            return;
        }

        // Check for a valid response
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code !== 200 ) {
            error_log('Error sending order data to Hub: HTTP ' . $response_code );
            return;
        }

        // Log successful data transmission
        error_log('Order data sent to Hub successfully: ' . print_r( $data, true ));
    }
}
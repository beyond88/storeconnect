<?php
/**
 * Plugin Name: StoreConnect - Real-Time Order Synchronization for WooCommerce
 * Description: StoreConnect synchronizes WooCommerce orders with your Hub for real-time data, enabling streamlined fulfillment and superior customer service.
 * Plugin URI: https://github.com/beyond88/storeconnect
 * Author: Mohiuddin Abdul Kader
 * Author URI: https://github.com/beyond88
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       storeconnect
 * Domain Path:       /languages
 * Requires PHP:      5.6
 * Requires at least: 4.4
 * Tested up to:      6.5.2
 * @package StoreConnect
 *
 * WC requires at least: 3.1
 * WC tested up to:   8.8.2
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class StoreConnect {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * Class constructor
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

    }

    /**
     * Initializes a singleton instance
     *
     * @return \StoreConnect
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'STORECONNECT_VERSION', self::version );
        define( 'STORECONNECT_FILE', __FILE__ );
        define( 'STORECONNECT_PATH', __DIR__ );
        define( 'STORECONNECT_URL', plugins_url( '', STORECONNECT_FILE ) );
        define( 'STORECONNECT_ASSETS', STORECONNECT_URL . '/assets' );
        define( 'STORECONNECT_BASENAME', plugin_basename( __FILE__ ) );
        define( 'STORECONNECT_PLUGIN_NAME', 'StoreConnect' );
        define( 'STORECONNECT_MINIMUM_PHP_VERSION', '5.6.0' );
        define( 'STORECONNECT_MINIMUM_WP_VERSION', '4.4' );
        define( 'STORECONNECT_MINIMUM_WC_VERSION', '3.1' );

    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        new StoreConnect\Assets();
        new StoreConnect\StoreConnecti18n();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new StoreConnect\Ajax();
        }

        if ( is_admin() ) {
            new StoreConnect\Admin();
        } else {
            new StoreConnect\Frontend();
        }

    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $installer = new StoreConnect\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 */
function storeconnect() {
    return StoreConnect::init();
}

// kick-off the plugin
storeconnect();
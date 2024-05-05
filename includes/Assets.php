<?php

namespace StoreConnect;

/**
 * Assets handlers class
 */
class Assets
{

    /**
     * Class constructor
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));
    }

    /**
     * All available scripts
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_admin_scripts()
    {
        return array(
            'storeconnect-vue-script' => array(
                'src'     => STORECONNECT_ASSETS . '/js/vue.min.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/vue.min.js'),
                'deps'    => array(),
            ),
            'storeconnect-axios-script' => array(
                'src'     => STORECONNECT_ASSETS . '/js/axios.min.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/axios.min.js'),
                'deps'    => array(),
            ),
            'storeconnect-order-sync-script' => array(
                'src'     => STORECONNECT_ASSETS . '/js/order-sync.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/order-sync.js'),
                'deps'    => array(),
            ),

        );
    }

    /**
     * All available styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function get_admin_styles()
    {
        return array(
            'storeconnect-admin-style' => array(
                'src'     => STORECONNECT_ASSETS . '/css/admin.css',
                'version' => filemtime(STORECONNECT_PATH . '/assets/css/admin.css'),
            ),
        );
    }

    /**
     * Register scripts and styles
     *
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  array
     */
    public function register_admin_assets($hook)
    {

        //if ($hook === 'woocommerce_page_wc-settings') {

        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        $section = isset($_GET['section']) ? $_GET['section'] : '';

        // Check if the tab is 'settings_tab_storeconnect' and the section is 'sync'
        //if ($tab === 'settings_tab_storeconnect' && $section === 'sync') {

        $scripts = $this->get_admin_scripts();
        $styles  = $this->get_admin_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';
            wp_enqueue_script($handle, $script['src'], $deps, $script['version'], true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_style($handle, $style['src'], $deps, $style['version']);
        }
        //}
        //}
    }
}

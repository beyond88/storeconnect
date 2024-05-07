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
            'storeconnect-axios' => array(
                'src'     => STORECONNECT_ASSETS . '/js/axios.min.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/axios.min.js'),
                'deps'    => array(),
            ),
            'vue-min' => array(
                'src'     => STORECONNECT_ASSETS . '/js/vue.min.js',
                'version' => '2.6.14',
                'deps'    => array('jquery'),
            ),
            'order-sync' => array(
                'src'     => STORECONNECT_ASSETS . '/js/order-sync.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/order-sync.js'),
                'deps'    => array('vue-min-js'),
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

        $scripts = $this->get_admin_scripts();
        $styles  = $this->get_admin_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';
            wp_enqueue_script($handle, $script['src'], $deps, $script['version'], true, true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $type = isset($script['type']) ? $script['type'] : '';

            wp_enqueue_style($handle, $style['src'], $deps, $style['version']);
        }
    }
}

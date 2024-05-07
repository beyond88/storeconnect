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
            'storeconnect-manifest' => array(
                'src'     => STORECONNECT_ASSETS . '/js/manifest.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/manifest.js'),
                'deps'    => array(),
            ),
            'storeconnect-vendor' => array(
                'src'     => STORECONNECT_ASSETS . '/js/vendor.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/vendor.js'),
                'deps'    => array(),
            ),
            'storeconnect-admin' => array(
                'src'     => STORECONNECT_ASSETS . '/js/admin.js',
                'version' => filemtime(STORECONNECT_PATH . '/assets/js/admin.js'),
                'deps'    => array('storeconnect-vendor'),
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

        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        $section = isset($_GET['section']) ? $_GET['section'] : '';

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

        wp_localize_script('storeconnect-admin', 'storeconnectLocalizer', array());
    }
}

<?php

namespace StoreConnect\Admin;

use StoreConnect\API\StoreConnectAPI;
use StoreConnect\Traits\Singleton;

class StoreConnectSettings
{

    use Singleton;
    protected $id = 'settings_tab_storeconnect';

    /* Bootstraps the class and hooks required actions & filters.
     *
     */
    public function init()
    {
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_Tab'), 50);
        add_action('woocommerce_settings_tabs_' . $this->id, array($this, 'settings_tab'));
        add_action('woocommerce_update_options_' . $this->id, array($this, 'update_settings'));
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_admin_field_sync_table', array($this, 'print_sync_table'));
    }

    /**
     * Retrieves the sections for the current settings tab.
     *
     * @return array An array of sections for the settings tab.
     */
    public function get_sections()
    {
        $sections = $this->get_own_sections();
        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * Retrieves the sections for the current settings tab.
     *
     * @return array An array of sections for the settings tab.
     */
    protected function get_own_sections()
    {

        $is_enable = get_option('wc_settings_tab_storeconnect_is_enable');

        $tabs = [
            'general' => __('General', 'storeconnect')
        ];

        if ($is_enable == 'yes') {
            $tabs['sync'] = __('Sync', 'storeconnect');
        }

        return $tabs;
    }

    /**
     * Output sections for the current settings tab.
     *
     * @since   1.0.0
     * @access  public
     * @global  string $current_section The current section.
     * @return  void
     */
    public function output_sections()
    {
        global $current_section;

        $sections = $this->get_sections();

        if (empty($sections) || 1 === count($sections)) {
            return;
        }

        echo '<ul class="subsubsub">';

        $array_keys = array_keys($sections);

        foreach ($sections as $id => $label) {
            $url       = admin_url('admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title($id));
            $class     = ($current_section === $id ? 'current' : '');
            $separator = (end($array_keys) === $id ? '' : '|');
            $text      = esc_html($label);
            echo "<li><a href='$url' class='$class'>$text</a> $separator </li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        echo '</ul><br class="clear" />';
    }

    /**
     * Output the HTML for the settings.
     *
     * @since 1.0.0
     */
    public function output()
    {
        global $current_section;
        $settings = $this->get_settings($current_section);
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * Adds a new settings tab to the WooCommerce settings tabs array.
     *
     * @since 1.0.0
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public function add_settings_Tab($settings_tabs)
    {
        $settings_tabs['settings_tab_storeconnect'] = __('StoreConnect', 'storeconnect');
        return $settings_tabs;
    }


    /**
     * Outputs settings using the WooCommerce admin fields API via the woocommerce_admin_fields() function.
     *
     * @since 1.0.0
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public function settings_tab()
    {
        global $current_section;

        $settings = $this->get_settings($current_section);
        woocommerce_admin_fields($settings);
    }

    /**
     * Saves settings using the WooCommerce options API via the woocommerce_update_options() function.
     *
     * @since 1.0.0
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public function update_settings()
    {
        woocommerce_update_options(self::get_settings());
    }


    /**
     * Retrieve settings for the StoreConnect plugin.
     *
     * @since 1.0.0
     * @param string|null $section The section for which settings are being retrieved.
     * @return array An array of settings for the specified section.
     */
    public function get_settings($section = null)
    {

        switch ($section) {

            case 'general':
                $settings = array(
                    'section_title' => array(
                        'name'     => __('StoreConnect integration', 'storeconnect'),
                        'type'     => 'title',
                        'desc'     => '',
                        'id'       => 'wc_settings_tab_storeconnect_title_general'
                    ),
                    'is_enable' => array(
                        'name' => __('Enable/Disable sync', 'storeconnect'),
                        'type' => 'checkbox',
                        'desc' => __('Check to enable', 'storeconnect'),
                        'id'   => 'wc_settings_tab_storeconnect_is_enable'
                    ),
                    'base_url' => array(
                        'name' => __('Base Url', 'storeconnect'),
                        'type' => 'text',
                        'desc_tip' => __('For development mode: https://api.example.com', 'storeconnect'),
                        'desc'        => sprintf(__('API base URL. (<a href="%s" target="_blank">https://example.com</a>).', 'storeconnect'), 'https://example.com'),
                        'id'   => 'wc_settings_tab_storeconnect_base_url'
                    ),

                    'section_end' => array(
                        'type' => 'sectionend',
                        'id' => 'wc_settings_tab_storeconnect_end-general'
                    )
                );

                break;
            case 'sync':
                $settings = array(
                    'section_sync_title' => array(
                        'name'     => __('Sync settings for existing data', 'storeconnect'),
                        'type'     => 'title',
                        'desc'     => __('Send existing orders to HubCentral', 'storeconnect'),
                        'id'       => 'wc_settings_tab_storeconnect_section_title_sync'
                    ),

                    'section_sync_table' => array(
                        'name' => __('', 'storeconnect'),
                        'type' => 'sync_table',
                        'desc' => __('', 'storeconnect'),
                        'id'   => 'wc_settings_tab_storeconnect_sync_table'
                    ),

                    'section_end' => array(
                        'type' => 'sectionend',
                        'id' => 'wc_settings_tab_storeconnect_section_end-sync'
                    )
                );
                break;
            default:
                $settings = array(
                    'section_title' => array(
                        'name'     => __('StoreConnect integration', 'storeconnect'),
                        'type'     => 'title',
                        'desc'     => '',
                        'id'       => 'wc_settings_tab_storeconnect_title_general'
                    ),
                    'is_enable' => array(
                        'name' => __('Enable/Disable sync', 'storeconnect'),
                        'type' => 'checkbox',
                        'desc' => __('Check to enable', 'storeconnect'),
                        'id'   => 'wc_settings_tab_storeconnect_is_enable'
                    ),
                    'base_url' => array(
                        'name' => __('Base Url', 'storeconnect'),
                        'type' => 'text',
                        'desc_tip' => __('For development mode: https://api.example.com', 'storeconnect'),
                        'desc'        => sprintf(__('API base URL. (<a href="%s" target="_blank">https://example.com</a>).', 'storeconnect'), 'https://example.com'),
                        'id'   => 'wc_settings_tab_storeconnect_base_url'
                    ),
                    'section_end' => array(
                        'type' => 'sectionend',
                        'id' => 'wc_settings_tab_storeconnect_end-general'
                    )
                );
        }

        if (!get_option('wc_settings_tab_storeconnect_is_enable')) {
            update_option('wc_settings_tab_storeconnect_is_enable', 'yes');
        }

        if (!get_option('wc_settings_tab_storeconnect_base_url')) {
            update_option('wc_settings_tab_storeconnect_base_url', 'https://api.example.com');
        }

        return apply_filters('wc_settings_tab_storeconnect', $settings, $section);
    }

    /**
     * Generate the opening HTML for the custom row table.
     *
     * @since 1.0.0
     * @access private
     * @return string The opening HTML for the table.
     */
    private function custom_row_table_header()
    {
        return '<table class="form-table">
            <tbody>';
    }

    /**
     * Generate the closing HTML for the custom row table.
     *
     * @since 1.0.0
     * @access private
     * @return string The closing HTML for the table.
     */
    private function custom_row_table_footer()
    {
        return '</tbody></table>';
    }

    /**
     * Generate custom rows HTML for a table.
     *
     * @since 1.0.0
     * @access private
     * @param array $rows The rows data.
     * @return string The HTML for the custom rows.
     */
    private function custom_rows($rows)
    {
        $table = $this->custom_row_table_header();
        foreach ($rows as $row) {
            $table .= '<tr valign="top">
                    <th scope="row" class="titledesc" style="width: 30%">' . $row['th'] . '</th>
                    <td class="forminp forminp-text" style="width: 10%">' . $row['td'] . '</td>
                    <td class="forminp forminp-text" style="width: 50%">' . $row['td_extra'] . '</td>
                </tr>';
        }

        $table .= $this->custom_row_table_footer();
        return $table;
    }

    /**
     * Print the synchronization table HTML.
     *
     * @since 1.0.0
     * @access public
     */
    public function print_sync_table()
    {

        $rows = [
            [
                'th' => '<label>' . __('WooCommerce &#128073; HubCentral', 'storeconnect') . '</label>',
                'td' => '<button id="woo-storeconnect-order-sync-btn" type="button" class="wt_button">
                            <span>' . __('&#10004 Sync now', 'storeconnect') . '</span>
                        </button><span class="sync_message" id="sync_order_message"></span>
                ',
                'td_extra' => '<button id="woo-storeconnect-order-stop-sync-btn" type="button" class="wt_button_extra">
                            <span>' . __('Stop sync', 'storeconnect') . '</span>
                        </button>
                ',
            ]
        ];

        echo $this->custom_rows($rows);
    }
}

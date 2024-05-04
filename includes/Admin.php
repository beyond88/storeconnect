<?php
namespace StoreConnect;
use StoreConnect\Admin\StoreConnectSettings;
use StoreConnect\Admin\OrderManage;

/**
 * The admin class
 */
class Admin {

    /**
     * Initialize the class
     * 
     * @since   1.0.0
     * @access  public
     * @param   none
     * @return  void
     */
    function __construct() {
        StoreConnectSettings::instance()->init();
        OrderManage::instance()->init();
    }

}
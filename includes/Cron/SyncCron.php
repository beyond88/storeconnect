<?php

namespace StoreConnect\Cron;

use StoreConnect\Traits\Singleton;

class SyncCron
{
    use Singleton;

    /**
     * Constructor method for SyncCron class.
     *
     * Registers cron schedules and action hooks for order synchronization.
     */
    public function __construct()
    {
        add_filter('cron_schedules', array($this, 'storeconnect_add_schedules'));
        add_action('storeconnect_sync_orders_schedule', array($this, 'storeconnect_sync_orders_schedule_fn'));
    }

    /**
     * Adds custom cron schedules for order synchronization.
     *
     * @param array $schedules Array of existing cron schedules.
     * @return array Modified array of cron schedules.
     */
    public function storeconnect_add_schedules()
    {
        if (!isset($schedules["storeconnect_sync_5_min"])) {
            $schedules["storeconnect_sync_5_min"] = array(
                'interval' => 5 * 60,
                'display' => __('Once every 5 minutes')
            );
        }

        if (!isset($schedules["storeconnect_sync_1_min"])) {
            $schedules["storeconnect_sync_1_min"] = array(
                'interval' => 60,
                'display' => __('Once every 1 minutes')
            );
        }

        return $schedules;
    }

    /**
     * Action hook callback for scheduled order synchronization.
     *
     * @throws \Exception Exception thrown if synchronization fails.
     * @return void
     * @TODO use single event
     */
    public function storeconnect_sync_orders_schedule_fn()
    {
        error_log('storeconnect_sync_orders_schedule defined!');
        //OrderSyncToHubCentral::instance()->sync();
    }
}

<?php

add_action('storeconnect_sync_orders_schedule', 'storeconnect_sync_orders_schedule_fn');
/**
 * Action hook callback for scheduled order synchronization.
 *
 * @throws \Exception Exception thrown if synchronization fails.
 * @return void
 * @TODO use single event
 */
function storeconnect_sync_orders_schedule_fn()
{
    error_log('storeconnect_sync_orders_schedule defined!');
    //WooCommerceToTripletexOrder::instance()->sync();
}

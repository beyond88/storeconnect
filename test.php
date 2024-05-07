<?php

require(dirname(__FILE__) . '/../../../wp-config.php');

require './vendor/autoload.php';

use StoreConnect\Cron\OrderSyncToHubCentral;

$sync = new OrderSyncToHubCentral();
$sync->sync();

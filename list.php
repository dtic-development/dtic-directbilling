<?php
require_once('directbilling_rest.php');
@session_start();

define('DTIC_API_KEY', 'bad6ee38a4b4b6e74c0dd968a6d5fa90');

$billing = new DirectBilling(DTIC_API_KEY);

$subscriptions = $billing->listSubscriptions();

print_r($subscriptions);

die('END');

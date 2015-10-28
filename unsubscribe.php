<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('directbilling.php');
@session_start();

define('DTIC_API_KEY', 'bad6ee38a4b4b6e74c0dd968a6d5fa90');

$billing = new DirectBilling(DTIC_API_KEY);

$subscription = $billing->checkSubscription();

if($subscription['status'] == 0) {
    $status = $billing->terminateSubscription($subscription['token']);
    echo "Terminate [$status]";
}
else {
    echo "Subscription is not active";
}

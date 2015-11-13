<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('directbilling.php');
@session_start();

define('DTIC_API_KEY', '966d1e3b3f10b0f4c515e833fd3ea966');

$billing = new DirectBilling(DTIC_API_KEY);

$subscription = $billing->checkSubscription();

if($subscription['status'] == 0) {

    $status = $billing->terminateSubscription($subscription['token']);
    echo "Terminate [$status] [token:{$subscription['token']}]";
}
else {
    echo "Subscription is not active";
}

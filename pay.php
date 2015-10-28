<?php
require_once('directbilling.php');
@session_start();

define('DTIC_API_KEY', 'bad6ee38a4b4b6e74c0dd968a6d5fa90');


$billing = new DirectBilling(DTIC_API_KEY);

$subscription = $billing->checkSubscription('tracker');


if($subscription['status'] == 0) {
    $msg = "Subscription Active";
}
else {
    $msg = "Subscripción Inactiva [STATUS: {$subscription['status']}] [ERROR: {$subscription['token']}]";
}

echo $msg;
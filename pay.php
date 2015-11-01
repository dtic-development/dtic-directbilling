<?php
require_once('directbilling.php');
@session_start();

define('DTIC_API_KEY', '966d1e3b3f10b0f4c515e833fd3ea966');


$billing = new DirectBilling(DTIC_API_KEY);

$subscription = $billing->checkSubscription('tracker');

print_r($subscription);

if($subscription['status'] == 0) {
    $msg = "Subscription Active";
}
else {
    $msg = "Subscripción Inactiva [STATUS: {$subscription['status']}] [ERROR: {$subscription['token']}]";
}

echo $msg;
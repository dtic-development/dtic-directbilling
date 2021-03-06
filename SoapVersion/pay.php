<?php
require_once('directbilling.php');
@session_start();

define('DTIC_API_KEY', '');


$billing = new DirectBilling(DTIC_API_KEY);

$subscription = $billing->checkSubscription('tracker');

if($subscription['status'] == 0) {
    $msg = "Subscription Active";
}
else {
    $msg = "Subscripción Inactiva [STATUS: {$subscription['status']}] [ERROR: {$subscription['token']}]";
}

echo $msg;
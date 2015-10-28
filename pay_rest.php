<?php
require_once('directbilling_rest.php');
@session_start();

//define('DTIC_API_KEY', '');

if(empty($_GET['apiKey'])) {
    die('$_GET[apiKey] needed for test purposes');
}

$apiKey = $_GET['apiKey'];

$billing = new DirectBilling($apiKey);

$subscription = $billing->checkSubscription('tracker');

if($subscription['status'] == 0) {
    $msg = "Subscription Active";
}
else {
    $msg = "Subscripción Inactiva [STATUS: {$subscription['status']}] [ERROR: {$subscription['token']}]";
}

echo $msg;
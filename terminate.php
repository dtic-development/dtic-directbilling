<?php
require_once('directbilling.php');
@session_start();

define('DTIC_API_KEY', '966d1e3b3f10b0f4c515e833fd3ea966');

$billing = new DirectBilling(DTIC_API_KEY);

$subsId = $_GET['subscriptionId'];
$status = $billing->terminateSubscription(md5($subsId));

print_r($status);

<?php
require_once('directbilling.php');
@session_start();

$subsId = $_GET['subscriptionId'];
$status = $billing->terminateSubscription(md5($subsId));

print_r($status);

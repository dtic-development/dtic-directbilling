# dtic-directbilling

##Example integration:

```
billing = new DirectBilling(DTIC_API_KEY);

$subscription = $billing->checkSubscription();

//$subscription = $billing->checkSubscription('trackerOpcional');

if($subscription['status'] == 0) {
    $msg = "Subscription Active";
}
else {
    $msg = "Subscripción Inactiva [STATUS: {$subscription['status']}] [ERROR: {$subscription['token']}]";
}
```


.

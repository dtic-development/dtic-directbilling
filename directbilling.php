<?php
// Subscription status
define("SUBSCRIPTION_ACTIVE",0);
define("SUBSCRIPTION_ERROR",-1);
define("APIKEY_INVALID",-2);

define("TOKEN_VALID",0);
define("TOKEN_INVALID",-100);

define("URL","http://api.smspremium.net/createSession.php");
define("WS_CHECK","http://api.smspremium.net/wsCheckSubscription.php?wsdl");
define("WS_TERMINATE","http://api.smspremium.net/wsTerminateSubscription.php?wsdl");
define("WS_LIST","http://api.smspremium.net/wsListSubscriptions.php?wsdl");


Class DirectBilling {

    protected $apiKey;

    function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    function checkSubscription (){

        $apiKey = $this->apiKey;

        $status=SUBSCRIPTION_ACTIVE;
        $error= "";


        //Check if token has been provided on request if yes ensure it is correct calling a WS and store it in session
        if (isset($_REQUEST['token'])){
            $token=$_REQUEST['token'];
        }
        else{
            if (isset($_SESSION['token'])){
                $token=$_SESSION['token'];
            }
        }

        if (isset($_SESSION['token'])){
            $_SESSION['token']=null;
        }

        if (isset($token)){

            // Check subscription
            // Returns : 0  --> Valid token
            //           -1 --> Subscription error
            //           -2 --> Invalid api key
            //           -3 --> Invalid token

            $client = new SoapClient(WS_CHECK, array('trace' => true));

            try {
                $output = $client->checkSubscription(
                    $apiKey,
                    $token
                );
            }
            catch(Exception $e) {
                $this->logError($e);
                /*
                echo "Response:\n" . $client->__getLastResponse() . "\n";
                echo "REQUEST:\n" . htmlentities($client->__getLastRequest()) . "\n";
                */
            }

            // If the token is successful save it in session
            if ($output == TOKEN_VALID) {
                $_SESSION['token']=$token;
            }
            else {

                $token = SUBSCRIPTION_ERROR;
                if (isset($_SESSION['token'])){
                    $_SESSION['token']=null;
                }

                $url= $this->curPageURL();
                error_log ("Current url ".$url);
                $url = preg_replace('~(\?|&)token=[^&]*~','$1',$url);
                error_log ("Current url without token ".$url);

                $this->redirect(URL."?w=".$apiKey."&f=".$url);

            }

            return array('status' => $output, 'token' => $token);

        }
        else{
            //Token was not on the session neither on the request
            //Request a new one identifying the customer and/or creating a subscription
            $this->redirect(URL."?w=".$apiKey."&f=".$this->curPageURL());

        }
    }

    /**
     * Terminate subscription
     *
     * @param $token
     * @return int
     *
     * @return
     *	SUBSCRIPTION_INACTIVE_SUCCESFULLY --> 0
     *  INVALID_TOKEN --> -100
     *  SUBSCRIPTION_ALREADY_INACTIVE --> -200
     *	SUBSCRIPTION_NOTFOUND  --> -201
     *	SUBSCRIPTION_ERROR --> -202
     *
     */
    function terminateSubscription($token) {

        $apiKey = $this->apiKey;

        if ($token){

            $client = new SoapClient(WS_TERMINATE);

            $output = $client->terminateSubscription(
                $apiKey,
                $token
            );
            return $output;
        }
        else{
            return INVALID_TOKEN;
        }


    }

    /**
     * @return bool
     */
    function listSubscriptions() {

        $apiKey = $this->apiKey;

        $client = new SoapClient(WS_LIST, array('trace' => true));


        try {
            $output = $client->listSubscriptions(
                $apiKey
            );
        }
        catch(Exception $e) {
            echo "Response:\n" . $client->__getLastResponse() . "\n";

            echo "REQUEST:\n" . htmlentities($client->__getLastRequest()) . "\n";
            return false;
        }

        return $output;
    }

    /**
     * @param $url
     * @param bool $permanent
     */
    function redirect($url, $permanent = false) {
        if($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header('Location: '.$url);
        exit();
    }

    /**
     * @return string
     */
    function curPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    function logError($msg) {
        error_log($msg);
    }
}
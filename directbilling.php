<?php
// Subscription status
define('DEVEL', 0);
define("SUBSCRIPTION_ACTIVE",0);
define("SUBSCRIPTION_ERROR",-1);
define("APIKEY_INVALID",-2);

define("TOKEN_VALID",0);
define("TOKEN_INVALID",-100);

if(DEVEL) {
    define("URL","http://devel.smspremium.net/subscriptions/sessions/create/");
    define("WS_CHECK","http://devel.smspremium.net/api/subscriptions/check/[apiKey]/[token]");
    define("WS_TERMINATE","http://devel.smspremium.net/api/subscriptions/terminate/[apiKey]/[token]");
    define("WS_LIST","http://devel.smspremium.net/api/subscriptions/list/[apiKey]");
    define("CREATE_SUBSCRIPTION_URL","http://devel.smspremium.net/subscriptions/create");
}
else {
    define("URL","http://api.smspremium.net/subscriptions/sessions/create/");
    define("WS_CHECK","http://api.smspremium.net/api/subscriptions/check/[apiKey]/[token]");
    define("WS_TERMINATE","http://api.smspremium.net/api/subscriptions/terminate/[apiKey]/[token]");
    define("WS_LIST","http://api.smspremium.net/api/subscriptions/list/[apiKey]");
    define("CREATE_SUBSCRIPTION_URL","http://api.smspremium.net/subscriptions/create");
}



class DirectBilling {

    protected $apiKey;

    function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $service_url
     * @param $params
     * @return mixed
     */
    private function restGET($service_url, $params) {

        foreach($params as $name => $value) {
            $service_url = str_replace("[$name]", $value, $service_url);
        }

        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        $curl_json = json_decode($curl_response, true);

        return $curl_json;
    }

    /**
     * @return bool
     */
    private function getToken() {
        if (isset($_REQUEST['token'])) {
            return $_REQUEST['token'];
        }
        else{
            if (isset($_SESSION['token'])){
                return $_SESSION['token'];
            }
        }
        return false;
    }

    /**
     *
     */
    private function removeTokenFromSession() {
        if (isset($_SESSION['token'])){
            $_SESSION['token'] = null;
        }
    }

    public function cleanSession() {
        unset($_SESSION['token']);
    }

    public function isNewSubscription() {
        if(isset($_GET['action']) && $_GET['action'] == 'subscription') {
            if(isset($_GET['status']) && $_GET['status'] == 'active') {
                return true;
            }
        }
        return false;
    }



    /**
     * @param null $extra
     * @return array
     */
    public function checkSubscription($extra = null, $template = null) {
        $token = $this->getToken();
        //$this->removeTokenFromSession();

        if(!empty($token)) {
            $response = $this->restGET(WS_CHECK, array(
                'apiKey'    =>  $this->apiKey,
                'token'     =>  $token
            ));

            if(!empty($response) && $response['status'] == TOKEN_VALID) {
                $_SESSION['token'] = $token;
                return array('status' => $response['status'], 'id' => $response['id'], 'token' => $token, 'is_new' => $this->isNewSubscription());
            }
            else {
                $token = SUBSCRIPTION_ERROR;
                $currentUrlWithoutToken = preg_replace('~(\?|&)token=[^&]*~', '$1', $this->currentPageURL());

                $urlRedirect = URL."?w=".$this->apiKey."&f=".urlencode($currentUrlWithoutToken);

                if(!empty($extra)) {
                    $urlRedirect = $urlRedirect . "&extraInfo=".urlencode($extra);
                }
                if(!empty($landing)) {
                    $urlRedirect = $urlRedirect . "&template=".urlencode($template);
                }
                $this->redirect($urlRedirect);
            }
            return array('status' => $response['status'], 'token' => $token, 'is_new' => $this->isNewSubscription());
        }
        else {
            $urlRedirect = URL."?w=".$this->apiKey."&f=".urlencode($this->currentPageURL());
            if(!empty($extra)) {
                $urlRedirect = $urlRedirect . "&extraInfo=".urlencode($extra);
            }
            $this->redirect($urlRedirect);
        }
    }


    /**
     * @param $token
     * @return int
     */
    public function terminateSubscription($token) {
        if(!empty($token)) {
            $response = $this->restGET(WS_TERMINATE, array(
                'apiKey'    =>  $this->apiKey,
                'token'     =>  $token
            ));
            if(isset($response['status'])) {
                return $response['status'];
            }
        }
        return TOKEN_INVALID;
    }

    /**
     * @return bool|mixed
     */
    public function listSubscriptions() {
        $response = $this->restGET(WS_LIST, array(
            'apiKey'    =>  $this->apiKey
        ));
        if(isset($response)) {
            return $response;
        }
        return false;
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
    function currentPageURL() {
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
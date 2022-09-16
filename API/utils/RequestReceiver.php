<?php
    require_once __DIR__ . '/../JSONObject.php';

    class RequestReceiver
    {
        private function __construct()
        {
            
        }

        public static function receiveGET(JSONObject &$containerClass, int &$limit = 1) : bool
        {
            if ($_SERVER["REQUEST_METHOD"] !== "GET")
                return false;

            if (empty($_GET))
                return false;
            
            if (isset($_GET["limit"]))
                $limit = $_GET["limit"];
                
            $containerClass = $containerClass::Deserialize($_GET);

            return true;
        }

        public static function receivePOST(JSONObject &$containerClass) : bool
        {
            if ($_SERVER["REQUEST_METHOD"] !== "POST")
                return false;

            if (empty($_POST))
                return false;
            
            $containerClass = $containerClass::Deserialize($_POST);

            return true;
        }
    }
?>

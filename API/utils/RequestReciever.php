<?php
    require_once __DIR__ . '/../JSONObject.php';

    class RequestReciever
    {
        private function __construct()
        {
            
        }

        public static function recieveGET(JSONObject $containerClass) : JSONObject|false
        {
            if ($_SERVER["REQUEST_METHOD"] != "GET")
                return false;

            if (empty($_GET))
                return false;
            
            return $containerClass::Deserialize($_GET);
        }

        public static function recievePOST(JSONObject $containerClass) : JSONObject|false
        {
            if ($_SERVER["REQUEST_METHOD"] != "POST")
                return false;

            if (empty($_POST))
                return false;
            
            return $containerClass::Deserialize($_POST);
        }
    }
?>
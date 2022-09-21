<?php
    require_once __DIR__ . '/../JSONObject.php';

    class RequestReceiver
    {
        private function __construct()
        {
            
        }

        public static function receiveGET() : array|false
        {
            if ($_SERVER["REQUEST_METHOD"] !== "GET")
                return false;

            if (empty($_GET))
                return false;

            return $_GET;
        }

        public static function receivePOST() : array|false
        {
            if ($_SERVER["REQUEST_METHOD"] !== "POST")
                return false;

            if (empty($_POST))
                return false;

            return $_POST;
        }
    }
?>

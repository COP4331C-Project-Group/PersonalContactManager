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

        public static function recievePUT() : array|false
        {
            if ($_SERVER["REQUEST_METHOD"] !== "PUT")
                return false;
            
            $_PUT = json_decode(file_get_contents('php://input'), true);

            if (empty($_PUT))
                return false;

            return $_PUT;
        }

        public static function recieveDELETE() : array|false
        {
            if ($_SERVER["REQUEST_METHOD"] !== "DELETE")
                return false;
            
            $start = strpos($_SERVER["REQUEST_URI"], "?");
            if (!$start || ($start + 1) >= strlen($_SERVER["REQUEST_URI"]))
                return false;
            
            $parameters = substr($_SERVER["REQUEST_URI"], $start + 1);
            $parameters = explode("&", $parameters);

            $_DELETE = array();

            foreach($parameters as $parameter)
            {
                $pair = explode("=", $parameter, 2);

                if (count($pair) < 2)
                    return false;

                $_DELETE[$pair[0]] = $pair[1];
            }

            return $_DELETE;
        }
    }
?>

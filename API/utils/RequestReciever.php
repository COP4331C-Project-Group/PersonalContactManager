<?php
    require_once __DIR__ . '/../JSONObject.php';

    class RequestReciever
    {
        private function __construct()
        {
            
        }

        public static function recieveGET(JSONObject $containerClass) : JSONObject|false
        {
            if (empty($_GET))
                return false;
            
            return $containerClass::Deserialize($_GET);
        }
    }
?>
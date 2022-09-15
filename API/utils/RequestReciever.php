<?php
    class RequestReciever
    {
        private function __construct()
        {
            
        }

        public static function recievePayload()
        {
            return json_decode(file_get_contents('php://input'), true);
        }
    }
?>
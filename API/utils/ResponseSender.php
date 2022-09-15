<?php
    class ResponseSender
    {
        private function __construct()
        {
            
        }

        public static function sendResult($obj)
        {
            header('Content-type: application/json');
            echo $obj;
            Exit();
        }
    
        public static function sendError($err)
        {
            $retValue = '{"error":"' . $err . '"}';
            ResponseSender::sendResult( $retValue );
            Exit();
        }
    } 
?>
<?php
    class ResponseSender
    {
        private function __construct()
        {
            
        }

        public static function sendResult($data, $message = NULL)
        {
            ResponseSender::sendResponse(200, $message, $data);
            Exit();
        }
    
        public static function sendError($err)
        {
            ResponseSender::sendResponse(400, $err, NULL);
            Exit();
        }

        private static function sendResponse(int $statusCode, $statusMessage, $data)
        {
            header("HTTP/1.1 ". $statusCode);

            $response['status'] = $statusCode;
            $response['status_message'] = $statusMessage;
            $response['data'] = $data;

            $jsonResponse = json_encode($response, JSON_PRETTY_PRINT);
            echo $jsonResponse;
        }
    } 
?>
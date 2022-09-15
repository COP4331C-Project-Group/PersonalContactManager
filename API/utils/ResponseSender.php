<?php
    class ResponseSender
    {
        private function __construct()
        {
            
        }

        public static function send(ResponseCodes $response, $message = NULL, $data = NULL,)
        {
            ResponseSender::sendResponse($response->value, $message, $data);
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
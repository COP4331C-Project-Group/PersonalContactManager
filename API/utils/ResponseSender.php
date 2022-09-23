<?php
    require_once __DIR__ . '/ResponseCodes.php';

    class ResponseSender
    {
        private function __construct()
        {
            
        }

        public static function send(ResponseCodes $response, $message = NULL, $data = NULL,)
        {
            ResponseSender::sendResponse($response->value, $message, $data);
        }

        private static function sendResponse(int $statusCode, $statusMessage, $data)
        {
            header("HTTP/1.1 ". $statusCode);

            $response['status'] = $statusCode;
            $response['status_message'] = $statusMessage;
            $response['data'] = $data;

            $jsonResponse = json_encode($response, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            die ($jsonResponse);
        }
    } 
?>

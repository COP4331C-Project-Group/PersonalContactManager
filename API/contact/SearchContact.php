<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReciever.php';

    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/ContactAPI.php';

    require_once __DIR__ . '/../database/Database.php';

    $payload = RequestReciever::recievePayload();
    
    // Create connection
    $mysql = connectToDatabaseOrFail();

    if ($payload == null)
        ResponseSender::sendError("Payload is empty");
    
    if ($mysql == false)
        ResponseSender::sendError("Database Connection error");

    $query = $payload['query'];

    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->GetContact($query, 10);

    if ($result == false)
        ResponseSender::sendError("Couldn't find contact");
    else
        ResponseSender::sendResult(json_encode($result, JSON_PRETTY_PRINT));
?>

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
        ResponseSender::sendError("Database connection error");

    $contact = Contact::Deserialize($payload);

    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->DeleteContact($contact);

    if ($result == false)
        ResponseSender::sendError("Couldn't delete contact");
    else
        ResponseSender::sendResult("Success");
?>

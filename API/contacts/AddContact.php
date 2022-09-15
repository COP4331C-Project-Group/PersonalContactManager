<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReciever.php';

    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/ContactAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $contact = RequestReciever::recievePOST(new Contact());
    
    $mysql = connectToDatabaseOrFail();

    if ($contact == false)
        ResponseSender::sendError("Payload is empty");
    
    if ($mysql == false) 
        ResponseSender::sendError("Database connection error");
    
    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->CreateContact($contact);

    if ($result == false)
        ResponseSender::sendError("Couldn't create contact");
    else
        ResponseSender::sendResult($result);
?>

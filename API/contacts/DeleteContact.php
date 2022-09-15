<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/ContactAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $contact = RequestReceiver::receivePOST(new Contact());

    $database = new Database();

    $mysql = $database->connectToDatabase();

    if ($contact == false)
        ResponseSender::send(ResponseCodes::NO_CONTENT, "Missing request body");
    
    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->DeleteContact($contact);

    if ($result == false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Contact doesn't exist");
    else
        ResponseSender::send(ResponseCodes::OK);
?>

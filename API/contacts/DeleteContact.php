<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/Contact.php';
    require_once __DIR__ . '/model/ContactAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $contact = new Contact();

    if (!RequestReceiver::receivePOST($contact))
        ResponseSender::send(ResponseCodes::NO_CONTENT, "Missing request body");

    $database = new Database();

    $mysql = $database->connectToDatabase();
    
    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->DeleteContact($contact);

    if ($result === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Contact doesn't exist");
    else
        ResponseSender::send(ResponseCodes::OK);
?>

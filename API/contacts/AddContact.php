<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/Contact.php';
    require_once __DIR__ . '/model/ContactAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $payload = RequestReceiver::receivePOST();

    if ($payload === false)
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");

    $contact = Contact::Deserialize($payload);
    
    $database = new Database();
    $mysql = $database->connectToDatabase();
    
    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->CreateContact($contact);

    if ($result === false)
        ResponseSender::send(ResponseCodes::CONFLICT, "Couldn't create contact");
    else
        ResponseSender::send(ResponseCodes::CREATED, NULL, $result);
?>

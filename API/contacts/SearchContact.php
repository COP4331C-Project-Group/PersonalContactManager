<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReciever.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/ContactAPI.php';

    require_once __DIR__ . '/../database/Database.php';

    $contact = RequestReciever::recieveGET(new Contact());
    
    $mysql = connectToDatabaseOrFail();

    if ($contact == false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Missing request body");

    $query = !empty($contact->firstName) ? $contact->firstName : "";
    $query = $query . (!empty($contact->lastName) ? " " . $contact->lastName : ""); 
    $query = $query . (!empty($contact->phone) ? " " . $contact->phone : "");
    $query = $query . (!empty($contact->email) ? " " . $contact->email : "");

    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->GetContact($query, $contact->userID, 10);

    if ($result == false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Couldn't find contact");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
?>

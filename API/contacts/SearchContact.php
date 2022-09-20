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

    $payload = RequestReceiver::receiveGET();

    $limit = 1;
    if (isset($payload['limit']))
        $limit = $payload['limit'];
    
    if (!isset($payload['query']) || !isset($payload['userID']) || $payload === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Missing request body");

    $query = $payload['query'];
    $userID = $payload['userID'];

    $database = new Database();
    $mysql = $database->connectToDatabase();

    $contactAPI = new ContactAPI($mysql);

    $result = $contactAPI->GetContact($query, $userID, $limit);

    if ($result === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Couldn't find contact");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
?>

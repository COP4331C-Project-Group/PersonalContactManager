<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/Contact.php';
    require_once __DIR__ . '/model/ContactAPI.php';

    require_once __DIR__ . '/../images/model/ImageAPI.php';

    require_once __DIR__ . '/../database/Database.php';

    require_once __DIR__ . '/../server/ServerException.php';

    $payload = RequestReceiver::receiveGET();

    if (!isPayloadValid($payload))
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Missing request body");

    $query = $payload['query'];
    $userID = $payload['userID'];
    $page = $payload['page'];
    $itemsPerPage = $payload['itemsPerPage'];

    $database = new Database();

    try
    {
        $mysql = $database->connectToDatabase();
    }
    catch (ServerException $e)
    {
        ResponseSender::send(ResponseCodes::INTERNAL_SERVER_ERROR, $e->getMessage());
    }

    $contactAPI = new ContactAPI($mysql, new ImageAPI($mysql));

    try
    {
        $result = $contactAPI->GetContact($query, $userID, $page, $itemsPerPage);
    }
    catch (Error $e)
    {
        ResponseSender::send(ResponseCodes::INTERNAL_SERVER_ERROR, $e->getMessage());
    }

    if ($result === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Couldn't find contact");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
    
    function isPayloadValid($payload) : bool
    {
        return $payload !== false && isset($payload['query'], $payload['userID'], $payload['page'], $payload['itemsPerPage']);
    }
?>

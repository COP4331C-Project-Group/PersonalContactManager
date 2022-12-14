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

    $payload = RequestReceiver::recieveDELETE();

    if (!isPayloadValid($payload))
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");

    $contact = Contact::Deserialize($payload);

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
        $result = $contactAPI->DeleteContact($contact);
    }
    catch (Error $e)
    {
        ResponseSender::send(ResponseCodes::INTERNAL_SERVER_ERROR, $e->getMessage());
    }

    if (!$result)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Contact doesn't exist");
    else
        ResponseSender::send(ResponseCodes::OK);

    function isPayloadValid($payload) : bool
    {
        return $payload !== false && isset($payload['ID']);
    }
?>

<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/Contact.php';
    require_once __DIR__ . '/model/ContactAPI.php';

    require_once __DIR__ . '/../images/model/Image.php';
    require_once __DIR__ . '/../images/model/ImageAPI.php';

    require_once __DIR__ . '/../database/Database.php';

    require_once __DIR__ . '/../server/ServerException.php';

    $payload = RequestReceiver::receivePUT();

    if (!isPayloadValid($payload))
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");
    
    $contact = Contact::Deserialize($payload);

    $image = Image::create("png")
        ->setImageAsBase64($payload["contactImage"]);

    $contact->setContactImage($image);

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
        $result = $contactAPI->UpdateContact($contact);
    }
    catch(Error $e)
    {
        ResponseSender::send(ResponseCodes::INTERNAL_SERVER_ERROR, $e->getMessage());
    }

    if (!$result)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Contact doesn't exist");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
    
    function isPayloadValid($payload) : bool
    {
        return $payload !== false && isset($payload['ID'], $payload['firstName'], $payload['lastName'], $payload['phone'], $payload['email'], $payload['contactImage']);
    }
?>

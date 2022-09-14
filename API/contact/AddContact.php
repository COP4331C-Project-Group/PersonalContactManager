<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReciever.php';

    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/ContactAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $payload = getRequestInfo();
    
    // Create connection
    $mysql = connectToDatabaseOrFail();

    if ($payload == null)
        returnWithError("Payload is empty");
    else if ($mysql == false) 
        returnWithError("Database connection error");
    else 
    {
        $contact = Contact::Deserialize($payload);

        $contactAPI = new ContactAPI($mysql);

        $result = $contactAPI->CreateContact($contact);

        if ($result == false)
            returnWithError("Couldn't create contact");
        else
            sendResultInfoAsJson(json_encode($result, JSON_PRETTY_PRINT));
    }
?>

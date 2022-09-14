<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    
    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/ContactAPI.php';

    require_once __DIR__ . '/../database/Database.php';

    $payload = getRequestInfo();
    
    // Create connection
    $mysql = connectToDatabaseOrFail();

    if ($payload == null)
        returnWithError("Payload is empty");
    else
    {
        $query = $payload['query'];

        if ($mysql == false)
            returnWithError("Database Connection error");
        else
        {
            $contactAPI = new ContactAPI($mysql);

            $result = $contactAPI->GetContact($query, 10);

            if ($result == false)
                returnWithError("Couldn't find contact");
            else
                sendResultInfoAsJson(json_encode($result, JSON_PRETTY_PRINT));
        }
    }

    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
?>

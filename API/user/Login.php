<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';

    require_once __DIR__ . '/UserAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $payload = getRequestInfo();
     
    $mysql = connectToDatabaseOrFail();

    if ($payload == null)
        returnWithError("Payload is empty");
    else
    {
        $user = User::Deserialize($payload);

        if ($mysql == false)
            returnWithError("Database connection error");
        else
        {
            $userAPI = new UserAPI($mysql);

            $result = $userAPI->GetUserByUsername($user->username);

            if ($result == false)
                returnWithError("User doesn't exist.");
            else
            {
                if (strcmp($result->password, $user->password) != 0)
                    returnWithError("Incorrect password.");
                else
                    sendResultInfoAsJson(json_encode($result, JSON_PRETTY_PRINT));
            }
        }
    }

    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
?>

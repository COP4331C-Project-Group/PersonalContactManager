<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReciever.php';

    require_once __DIR__ . '/UserAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $payload = getRequestInfo();

    $mysql = connectToDatabaseOrFail();

    if ($payload == null)
        returnWithError("Payload is empty");
    else if ($mysql == false)
        returnWithError("Database Connection error");
    else
    {
        $user = User::Deserialize($payload);

        $userAPI = new UserAPI($mysql);

        if (userExists($user, $userAPI))
            returnWithError("User alredy exists");
        else
        {
            $result = $userAPI->CreateUser($user);

            if ($result == false)
                returnWithError("Couldn't create user");
            else
                sendResultInfoAsJson(json_encode($result, JSON_PRETTY_PRINT));
        }
    }

    function userExists(object $user, UserAPI $userAPI) : bool
    {
        $result = $userAPI->GetUserByUsername($user->username);
        
        if (is_object($result))
            return true;

        return false;
    }
?>

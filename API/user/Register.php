<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
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
    }

    function userExists(object $user, UserAPI $userAPI) : bool
    {
        $result = $userAPI->GetUserByUsername($user->username);
        
        if (is_object($result))
            return true;

        return false;
    }
    
    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson( $obj )
    {
        header('Content-type: application/json');
        echo $obj;
    }
    
    function returnWithError( $err )
    {
        $retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
?>

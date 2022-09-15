<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReciever.php';

    require_once __DIR__ . '/UserAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $user = RequestReciever::recieveGET(new User());

    $mysql = connectToDatabaseOrFail();

    if ($user == false)
        ResponseSender::sendError("Payload is empty");
    
    if ($mysql == false)
        ResponseSender::sendError("Database Connection error");
    
    $userAPI = new UserAPI($mysql);

    if (userExists($user, $userAPI))
        ResponseSender::sendError("User alredy exists");
        
    $result = $userAPI->CreateUser($user);

    if ($result == false)
        ResponseSender::sendError("Couldn't create user");
    else
        ResponseSender::sendResult($result);

    function userExists(object $user, UserAPI $userAPI) : bool
    {
        $result = $userAPI->GetUserByUsername($user->username);
        
        if (is_object($result))
            return true;

        return false;
    }
?>

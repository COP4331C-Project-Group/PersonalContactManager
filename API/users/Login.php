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
        ResponseSender::sendError("Missing request body");
    
    if ($mysql == false)
        ResponseSender::sendError("Database Connection error");

    $userAPI = new UserAPI($mysql);

    $result = $userAPI->GetUserByUsername($user->username);

    if ($result == false)
        ResponseSender::sendError("User doesn't exist.");

    if (strcmp($result->password, $user->password) != 0)
        ResponseSender::sendError("Incorrect password.");
    else
        ResponseSender::sendResult($result);
?>

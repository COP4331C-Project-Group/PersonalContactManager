<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReciever.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/UserAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $user = RequestReciever::recieveGET(new User());
     
    $database = new Database();

    $mysql = $database->connectToDatabase();

    if ($user == false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Missing request body");
    
    $userAPI = new UserAPI($mysql);

    $result = $userAPI->GetUserByUsername($user->username);

    if ($result == false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "User doesn't exist.");

    if (strcmp($result->password, $user->password) != 0)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Incorrect password.");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
?>

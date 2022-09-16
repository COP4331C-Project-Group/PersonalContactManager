<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/User.php';
    require_once __DIR__ . '/model/UserAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $user = new User(); 

    if (!RequestReceiver::receiveGET($user))
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Missing request body");

    $database = new Database();

    $mysql = $database->connectToDatabase();
    
    $userAPI = new UserAPI($mysql);

    $result = $userAPI->GetUserByUsername($user->username);

    if ($result === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "User doesn't exist.");

    if (strcmp($result->password, $user->password) != 0)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "Incorrect password.");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
?>

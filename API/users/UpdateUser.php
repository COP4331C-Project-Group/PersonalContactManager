<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/User.php';
    require_once __DIR__ . '/model/UserAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $payload = RequestReceiver::receivePOST();
    if ($payload === false || !isset($payload['ID']))
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");

    $user = User::Deserialize($payload);

    $database = new Database();
    $mysql = $database->connectToDatabase();
    
    $userAPI = new UserAPI($mysql);

    $result = $userAPI->UpdateUser($user);

    if ($result === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "User doesn't exist");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
?>

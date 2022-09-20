<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/User.php';
    require_once __DIR__ . '/model/UserAPI.php';

    require_once __DIR__ . '/../images/model/ImageAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $payload = RequestReceiver::receiveGET();

    if ($payload === false)
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");

    $user = User::Deserialize($payload);
    /**
     * Create:
     * Receive Image as Base64
     * Convert it to Image object
     * Assign Image object to User
     * send User to UserAPI
     */

    $database = new Database();
    $mysql = $database->connectToDatabase();
    
    $userAPI = new UserAPI($mysql, new ImageAPI($mysql));

    $result = $userAPI->GetUserByUsername($user->username);

    if ($result === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "User doesn't exist.");

    if (strcmp($result->password, $user->password) != 0)
        ResponseSender::send(ResponseCodes::FORBIDDEN, "Incorrect password.");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
?>

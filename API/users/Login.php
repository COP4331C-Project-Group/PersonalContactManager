<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/User.php';
    require_once __DIR__ . '/model/UserAPI.php';

    require_once __DIR__ . '/../database/Database.php';

    $payload = RequestReceiver::receiveGET();

    if ($payload === false)
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");

    $user = User::Deserialize($payload);

    $database = new Database();

    try
    {
        $mysql = $database->connectToDatabase();
    }
    catch (ServerException $e)
    {
        ResponseSender::send(ResponseCodes::INTERNAL_SERVER_ERROR, $e->getMessage());
    }
    
    $userAPI = new UserAPI($mysql);

    $result = $userAPI->GetUserByUsername($user->username);

    if ($result === false)
        ResponseSender::send(ResponseCodes::NOT_FOUND, "User doesn't exist.");

    if (strcmp($result->password, $user->password) != 0)
        ResponseSender::send(ResponseCodes::FORBIDDEN, "Incorrect password.");
    else
        ResponseSender::send(ResponseCodes::OK, NULL, $result);
?>

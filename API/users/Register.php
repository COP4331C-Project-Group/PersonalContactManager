<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/../utils/ResponseSender.php';
    require_once __DIR__ . '/../utils/RequestReceiver.php';
    require_once __DIR__ . '/../utils/ResponseCodes.php';

    require_once __DIR__ . '/model/User.php';
    require_once __DIR__ . '/model/UserAPI.php';
    
    require_once __DIR__ . '/../database/Database.php';

    $user = new User();

    if (!RequestReceiver::receivePOST($user))
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");

    $database = new Database();

    $mysql = $database->connectToDatabase();
    
    $userAPI = new UserAPI($mysql);

    if (userExists($user, $userAPI))
        ResponseSender::send(ResponseCodes::CONFLICT, "User already exists");
        
    $result = $userAPI->CreateUser($user);

    if ($result === false)
        ResponseSender::send(ResponseCodes::CONFLICT, "Couldn't create user");
    else
        ResponseSender::send(ResponseCodes::CREATED, NULL, $result);

    function userExists(object $user, UserAPI $userAPI) : bool
    {
        $result = $userAPI->GetUserByUsername($user->username);
        
        if (is_object($result))
            return true;

        return false;
    }
?>

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

    $payload = RequestReceiver::receivePOST();

    if ($payload === false)
        ResponseSender::send(ResponseCodes::BAD_REQUEST, "Missing request body");

    $user = User::Deserialize($payload);

    if ($payload["profileImage"] != "")
    {
        $image = Image::create(strval($user->ID), "png")
            ->setImageAsBase64($payload["profileImage"]);
        
        $user->setProfileImage($image);
    }

    $database = new Database();
    $mysql = $database->connectToDatabase();
    
    $userAPI = new UserAPI($mysql, new ImageAPI($mysql));

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

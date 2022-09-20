<?php
    require_once __DIR__ . '/User.php';
    require_once __DIR__ . '/../../images/model/ImageAPI.php';
    require_once __DIR__ . '/../../server/Server.php';

    class UserAPI 
    {
        private mysqli $mysql;
        private ImageAPI $imageAPI;

        public function __construct(mysqli $mysql, ImageAPI $imageAPI)
        {
            $this->mysql = $mysql;
            $this->imageAPI = $imageAPI;
        }

        /**
         * Creates user record.
         * 
         * @param object $user object of the User class.
         * @return object|false object of the User class containing all information about created record or false if operation was unsuccessful.
         */
        public function CreateUser(object $user) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            $image = false;
            if ($user->profileImage !== NULL)
                $image = $this->imageAPI->CreateImage($user->profileImage);

            $profileImageID = $image !== false ? $image->ID : NULL;

            $stmt = $this->mysql->prepare("INSERT INTO Users (ID, firstName, lastName, username, password, dateCreated, profileImageID) VALUES (DEFAULT, ?, ?, ?, ?, DEFAULT, ?)");
            $stmt->bind_param(
                "ssssi", 
                $user->firstName,
                $user->lastName,
                $user->username, 
                $user->password,
                $profileImageID
            );
            
            $result = $stmt->execute();

            if ($result !== false)
                return $this->GetUserByID($this->mysql->insert_id);

            return false;
        }

        /**
         * Gets user record by user's unique identifier.
         * 
         * @param int $userID unique user identifier.
         * @return object|false object of the User class containing all information about record or false if operation was unsuccessful. 
         */
        private function GetUserByID($userID) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $result = $this->mysql->query("SELECT * FROM Users WHERE ID=$userID");

            if ($result === false)
                return false;

            $record = $result->fetch_object();

            if ($record === null)
                return false;

            $user = User::Deserialize($record);
            
            if ($record->profileImageID != NULL) {
                $image = $this->imageAPI->GetImageByID($record->profileImageID);

                if ($image !== false)
                    $user->setProfileImage($image);
            }

            return $user;
        }

        /**
         * Gets user record by username.
         * 
         * @param string $username username of the user.
         * @return object|false object of the User class containing all information about record or false if operation was unsuccessful.
         */
        public function GetUserByUsername(string $username) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $result = $this->mysql->query("SELECT * FROM Users WHERE username='$username'");

            if ($result === false)
                return false;

            $record = $result->fetch_object();

            if ($record === null)
                return false;

            return $this->GetUserByID($record->ID);
        }

        /**
         * Updates user record.
         * 
         * @param object $user object of the User class.
         * @return object|false object of the User class containing all information about updated record or false if operation was unsuccessful.
         */
        public function UpdateUser(object $user) : object|false
        {
            if ($this->mysql->connect_error != null)
                return false;

            $result = $this->mysql->query("UPDATE Users SET firstName='$user->firstName', lastName='$user->lastName', password='$user->password' WHERE ID=$user->ID");

            if ($result !== false)
                return $this->GetUserByID($user->ID);

            return false;
        } 
    }
?>

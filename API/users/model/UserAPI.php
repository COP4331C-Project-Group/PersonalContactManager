<?php
    require_once __DIR__ . '/User.php';

    class UserAPI 
    {
        private mysqli $mysql;

        public function __construct(mysqli $mysql)
        {
            $this->mysql = $mysql;
        }

        /**
         * Creates user record.
         * 
         * @param object $user object of the User class.
         * @return object|false object of the User class containing all information about created record or false if operation was unsuccessful.
         */
        public function CreateUser(object $user) : object|false
        {
            if (!is_null($this->mysql->connect_error))
                return false;
            
            $stmt = $this->mysql->prepare("INSERT INTO Users (ID, firstName, lastName, username, password, dateCreated, lastLogin) VALUES (DEFAULT, ?, ?, ?, ?, DEFAULT, NULL)");
            $stmt->bind_param(
                "ssss", 
                $user->firstName,
                $user->lastName,
                $user->username, 
                $user->password
            );
            
            $result = $stmt->execute();

            if (!$result)
                return false;

            return $this->GetUserByID($this->mysql->insert_id);
        }

        /**
         * Gets user record by user's unique identifier.
         * 
         * @param int $userID unique user identifier.
         * @return object|false object of the User class containing all information about record or false if operation was unsuccessful. 
         */
        private function GetUserByID($userID) : object|false
        {
            if (!is_null($this->mysql->connect_error))
                return false;

            $result = $this->mysql->query("SELECT * FROM Users WHERE ID=$userID");

            if (!$result)
                return false;

            $record = $result->fetch_object();

            if (is_null($record))
                return false;
            
            return User::Deserialize($record);
        }

        /**
         * Gets user record by username.
         * 
         * @param string $username username of the user.
         * @return object|false object of the User class containing all information about record or false if operation was unsuccessful.
         */
        public function GetUserByUsername(string $username) : object|false
        {
            if (!is_null($this->mysql->connect_error))
                return false;

            $result = $this->mysql->query("SELECT * FROM Users WHERE username='$username'");

            if (!$result)
                return false;

            $record = $result->fetch_object();

            if (is_null($record))
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
            if (!is_null($this->mysql->connect_error))
                return false;

            $result = $this->mysql->query("UPDATE Users SET firstName='$user->firstName', lastName='$user->lastName', username='$user->username', password='$user->password' WHERE ID=$user->ID");

            if (!$result)
                return false;

            return $this->GetUserByID($user->ID);
        }
    }
?>

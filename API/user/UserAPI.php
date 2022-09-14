<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/User.php';

    class UserAPI 
    {
        private mysqli $mysql;

        public function __construct(mysqli $mysql)
        {
            $this->mysql = $mysql;
        }

        public function __destruct()
        {
            $this->mysql->close();
        }

        public function CreateUser(object $user) : object|false
        {
            if ($this->mysql->connect_error != null)
                return false;

            $stmt = $this->mysql->prepare("INSERT INTO Users (ID, firstName, lastName, username, password, dateCreated) VALUES (DEFAULT, ?, ?, ?, ?, DEFAULT)");
            $stmt->bind_param(
                "ssss", 
                $user->firstName,
                $user->lastName,
                $user->username, 
                $user->password
            );
            
            $result = $stmt->execute();

            if ($result)
                return $this->GetUserByID($this->mysql->insert_id);

            return false;
        }

        private function GetUserByID($userID) : object|false
        {
            if ($this->mysql->connect_error != null)
                return false;

            $result = $this->mysql->query("SELECT * FROM Users WHERE ID=$userID");

            if ($result == false)
                return false;

            $record = $result->fetch_object();

            if ($record == null)
                return false;

            return User::Deserialize($record);
        }

        public function GetUserByUsername(string $username) : object|false
        {
            if ($this->mysql->connect_error != null)
                return false;

            $result = $this->mysql->query("SELECT * FROM Users WHERE username='$username'");

            if ($result == false)
                return false;

            $record = $result->fetch_object();

            if ($record == null)
                return false;

            return User::Deserialize($record);
        }
    }
?>
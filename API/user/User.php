<?php
    require_once __DIR__ . "/../utils/JsonUtils.php";
    
    class User extends JsonDeserializer {
        public int $userID;

        public string $firstName;
        public string $lastName;
        public string $username;
        public string $password;
        public string $creationDate;

        public function __construct()
        {
            $this->firstName = "";
            $this->lastName = "";
            $this->username = "";
            $this->password = "";
        }

        public static function create(
            string $firstName, 
            string $lastName, 
            string $username, 
            string $password) : User 
        {
            $instance = new self();
            $instance->firstName = $firstName;
            $instance->lastName = $lastName;
            $instance->username = $username;
            $instance->password = $password;

            return $instance;
        }

        public function setCreationDate($creationDate) : User
        {
            $this->creationDate = $creationDate;
            return $this;
        }

        public function setUserID($userID) : User
        {
            $this->userID = $userID;
            return $this;
        }

        public function setFirstName($firstName) : User
        {
            $this->firstName = $firstName;
            return $this;
        }

        public function setLastName($lastName) : User
        {
            $this->lastName = $lastName;
            return $this;
        }

        public function setUsername($username) : User
        {
            $this->username = $username;
            return $this;
        }

        public function setPassword($password) : User
        {
            $this->password = $password;
            return  $this; 
        }

        public function getJSON()
        {
            $jsonArray = array(
                "FirstName" => $this->firstName, 
                "LastName" => $this->lastName,
                "Username" => $this->username,
                "Password" => $this->password,
                "CreationDate" => $this->creationDate
            );

            return json_encode($jsonArray, JSON_PRETTY_PRINT);
        }
    }
?>
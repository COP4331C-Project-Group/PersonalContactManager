<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    
    class User extends JsonDeserializer {
        public int $ID;

        public string $firstName;
        public string $lastName;
        public string $username;
        public string $password;
        public string $dateCreated;

        public function __construct()
        {
            $this->ID = -1;
            $this->firstName = "";
            $this->lastName = "";
            $this->username = "";
            $this->password = "";
            $this->dateCreated = "";
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

        public function setDateCreated($dateCreated) : User
        {
            $this->dateCreated = $dateCreated;
            return $this;
        }

        public function setID($userID) : User
        {
            $this->ID = $userID;
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
                "ID" => $this->ID,
                "firstName" => $this->firstName, 
                "lastName" => $this->lastName,
                "username" => $this->username,
                "password" => $this->password,
                "dateCreated" => $this->dateCreated
            );

            return json_encode($jsonArray, JSON_PRETTY_PRINT);
        }
    }
?>
<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';

    class Contact extends JSONObject
    {
        public int $ID;
        public int $userID;
        
        public string $firstName;
        public string $lastName;
        public string $phone;
        public string $email;

        public function __construct()
        {
            $this->firstName = "";
            $this->lastName = "";
            $this->phone = "";
            $this->userID = -1;
            $this->email = "";
            $this->ID = -1;
        }

        public static function create(
            string $firstName, 
            string $lastName, 
            string $phone, 
            int $userID, 
            string $email) : Contact 
        {
            $instance = new self();
            $instance->firstName = $firstName;
            $instance->lastName = $lastName;
            $instance->phone = $phone;
            $instance->userID = $userID;
            $instance->email = $email;
            
            return $instance;
        }

        public function setID(int $contactID) : Contact
        {
            $this->ID = $contactID;
            return $this;
        }

        public function setUserID(int $userID) : Contact
        {
            $this->userID = $userID;        
            return $this;
        }

        public function setFirstName(string $firstName) : Contact
        {
            $this->firstName = $firstName;
            return $this;
        }

        public function setLastName(string $lastName) : Contact
        {
            $this->lastName = $lastName;
            return $this;
        }

        public function setPhone(string $phone) : Contact
        {
            $this->phone = $phone;
            return $this;
        }

        public function setEmail(string $email) : Contact
        {
            $this->email = $email;
            return $this;
        }

        public function jsonSerialize(): mixed
        {
            return [
                "ID" => $this->ID,
                "firstName" => $this->firstName, 
                "lastName" => $this->lastName,
                "phone" => $this->phone,
                "email" => $this->email,
                "userID" => $this->userID
            ];
        }
    }
?>
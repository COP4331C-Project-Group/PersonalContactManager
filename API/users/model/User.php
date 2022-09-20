<?php
    require_once __DIR__ . '/../../JSONObject.php';
    require_once __DIR__ . '/../../images/model/Image.php';
    
    class User extends JSONObject
    {
        public int $ID;

        public string $firstName;
        public string $lastName;
        public string $username;
        public string $password;
        public string $dateCreated;

        public Image $profileImage;

        public function __construct()
        {
            $this->ID = -1;
            $this->profileImage = NULL;
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

        public function setProfileImage(Image $image) : User
        {
            $this->image = $image;
            return $this;
        }

        public function setDateCreated(string $dateCreated) : User
        {
            $this->dateCreated = $dateCreated;
            return $this;
        }

        public function setID(int $userID) : User
        {
            $this->ID = $userID;
            return $this;
        }

        public function setFirstName(string $firstName) : User
        {
            $this->firstName = $firstName;
            return $this;
        }

        public function setLastName(string $lastName) : User
        {
            $this->lastName = $lastName;
            return $this;
        }

        public function setUsername(string $username) : User
        {
            $this->username = $username;
            return $this;
        }

        public function setPassword(string $password) : User
        {
            $this->password = $password;
            return  $this; 
        }

        public function jsonSerialize(): mixed
        {
            return [
                "ID" => $this->ID,
                "firstName" => $this->firstName, 
                "lastName" => $this->lastName,
                "username" => $this->username,
                "password" => $this->password,
                "dateCreated" => $this->dateCreated,
                "profileImage" => $this->profileImage->imageAsBase64
            ];
        }
    }
?>

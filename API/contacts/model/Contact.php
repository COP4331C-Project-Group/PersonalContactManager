<?php
    require_once __DIR__ . '/../../JSONObject.php';
    require_once __DIR__ . '/../../images/model/Image.php';

    class Contact extends JSONObject
    {
        public int $ID;
        public int $userID;
        
        public string $firstName;
        public string $lastName;
        public string $phone;
        public string $email;
        public string $dateCreated;

        public ?Image $contactImage;

        public function __construct()
        {   
            $this->contactImage = NULL;
            $this->firstName = "";
            $this->lastName = "";
            $this->phone = "";
            $this->userID = -1;
            $this->email = "";
            $this->ID = -1;
            $this->dateCreated = "";
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

        public function setDateCreated(string $dateCreated) : Contact
        {
            $this->dateCreated = $dateCreated;
            return $this;
        }

        public function setContactImage(Image $image) : Contact
        {
            $this->contactImage = $image;
            return $this;
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

        public function __toString()
        {
            return $this->firstName
                . $this->lastName
                . strval($this->ID)
                . $this->phone
                . $this->email
                . $this->dateCreated
                . strval($this->userID)
                . $this->contactImage->imageAsBase64; 
        }

        public function jsonSerialize(): mixed
        {
            return [
                "ID" => $this->ID,
                "firstName" => $this->firstName, 
                "lastName" => $this->lastName,
                "phone" => $this->phone,
                "email" => $this->email,
                "dateCreated" => $this->dateCreated,
                "userID" => $this->userID,
                "hasImage" => !is_null($this->contactImage) ? true : false
            ];
        }
    }
?>

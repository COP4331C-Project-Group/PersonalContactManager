<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/ContactSearch.php';

    class ContactAPI {
        private mysqli $mysql;

        public function __construct(mysqli $mysql)
        {
            $this->mysql = $mysql;
        }

        public function __destruct()
        {
            $this->mysql->close();
        }

        public function CreateContact(object $contact) : object|false 
        {
            if ($this->mysql->connect_error != null)
                return false;

            $stmt = $this->mysql->prepare("INSERT INTO Contacts (ID, firstName, lastName, email, phone, userID) VALUES(DEFAULT, ?, ?, ?, ?, ?)");
		    $stmt->bind_param(
                "sssss", 
                $contact->firstName, 
                $contact->lastName, 
                $contact->email, 
                $contact->phone, 
                $contact->userID
            );
			
		    $result = $stmt->execute();

            if ($result)
                return $this->GetContactByID($this->mysql->insert_id);

            return false;
        }

        private function GetContactByID($contactID) : object|false
        {
            if ($this->mysql->connect_error != null)
                return false;

            $result = $this->mysql->query("SELECT * FROM Contacts WHERE ID=$contactID");

            if ($result == false)
                return false;
        
            return Contact::Deserialize($result->fetch_object());
        }

        public function GetContact(object $contact, ContactSearch $searchOption, int $numOfResults) : object|false
        {
            if ($this->mysql->connect_error)
                return false;
        
            switch($searchOption) 
            {
                case ContactSearch::BY_FIRST_NAME:
                    return $this->GetContactByFirstName($contact, $numOfResults);
                case ContactSearch::BY_LAST_NAME:
                    return $this->GetContactByLastName($contact, $numOfResults);
                case ContactSearch::BY_FIRST_NAME_AND_LAST_NAME:
                    return $this->GetContactByFirstNameAndLastName($contact, $numOfResults);
                default:
                    return false;
            }
        }

        private function GetContactByFirstName(object $contact, int $numOfResults) : object|false
        {
            if ($this->mysql->connect_error)
                return false;

            $searchQuery = $contact->firstName . "%";
            $result = $this->mysql->query("SELECT * FROM Contacts WHERE firstName like $searchQuery LIMIT=$numOfResults");

            if ($result == false)
                return false;

            return Contact::DeserializeArray($result);
        }


        private function GetContactByLastName(object $contact, int $numOfResults) : object|false
        {
            if ($this->mysql->connect_error)
                return false;

            $searchQuery = $contact->lastName . "%";
            $result = $this->mysql->query("SELECT * FROM Contacts WHERE lastName like $searchQuery LIMIT=$numOfResults");

            if ($result == false)
                return false;

            return Contact::DeserializeArray($result);
        }

        private function GetContactByFirstNameAndLastName(object $contact, int $numOfResults) : object|false
        {
            if ($this->mysql->connect_error)
                return false;

            $searchQueryFirstName = $contact->firstName . "%";
            $searchQueryLastName = $contact->lastName . "%";
            $result = $this->mysql->query("SELECT * FROM Contacts WHERE firstName like $searchQueryFirstName AND lastName like $searchQueryLastName LIMIT=$numOfResults");

            if ($result == false)
                return false;

            return Contact::DeserializeArray($result);
        }

        private function UpdateContact(object $contact) : bool
        {
            if ($this->mysql->connect_error)
                return false;
        
            $result = $this->mysql->query("UPDATE FROM Contacts SET firstName=$contact->firstName, lastName=$contact->lastName, email=$contact->email, phone=$contact->phone, userID=$contact->userID WHERE ID=$contact->ID");
        
            return $result;
        }

        private function DeleteContact(object $contact) : bool
        {
            if ($this->mysql->connect_error)
                return false;

            $result = $this->mysql->query("DELETE FROM Contacts WHERE ID=$contact->ID");

            return $result;
        }
    }
?>
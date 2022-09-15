<?php
    require_once __DIR__ . '/../utils/JsonUtils.php';

    class ContactAPI 
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

        /**
         * Creates contact record.
         * 
         * @param object $contact object of the Contact class.
         * @return object|false object of the Contact class containing all information about created record or false if operation was unsuccessful. 
         */
        public function CreateContact(object $contact) : object|false 
        {
            if ($this->mysql->connect_error != null)
                return false;

            $stmt = $this->mysql->prepare("INSERT INTO Contacts (ID, firstName, lastName, email, phone, userID) VALUES(DEFAULT, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssssi", 
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

        /**
         * Gets contact record by contact's unique identifier.
         * 
         * @param int $contactID unique contact identifier.
         * @return object|false object of the Contact class containing all information about record or false if operation was unsuccessful.
         */
        private function GetContactByID($contactID) : object|false
        {
            if ($this->mysql->connect_error != null)
                return false;

            $result = $this->mysql->query("SELECT * FROM Contacts WHERE ID=$contactID");

            if ($result == false)
                return false;
        
            $record = $result->fetch_object();

            if ($record == null)
                return false;

            return Contact::Deserialize($record);
        }

        /**
         * Gets contact record which satisfies query.
         * 
         * @param string $query search query used for searching for set of records in the database.
         * @param int $numOfResults max number of results that satisfy search query to be returned if search is successful.
         * @param int $userID unique userIdentificator.
         * @return array|false array of objects of the Contact class containing all information about each individual record or false if operation was unsuccessful.
         */
        public function GetContact(string $query, int $userID, int $numOfResults) : array|false
        {
            if ($this->mysql->connect_error != null)
                return false;

            $queryArray = explode(" ", $query);

            $searchQuery = "";

            foreach ($queryArray as $word) {
                $searchQuery = $searchQuery . 
                "firstName LIKE '%$word%' OR 
                lastName LIKE '%$word%' OR
                phone LIKE '%$word%' OR
                email LIKE '%$word%'" . " OR ";
            }

            // Removes the last " OR " inside of the searchQuery
            $searchQuery = substr($searchQuery, 0, strlen($searchQuery) - 4);
        
            $result = $this->mysql->query("SELECT * FROM Contacts WHERE ($searchQuery) AND userID=$userID LIMIT $numOfResults");
        
            if ($result == false)
                return false;
        
            $resultArray = [];

            while($record = $result->fetch_object())
                $resultArray[] = Contact::Deserialize($record);
                
            return $resultArray;
        }

        /**
         * Updates contact record.
         * 
         * @param object $contact contact object of the Contact class.
         * @return object|false contact object of the Contact class containing updated information or false if operation was unsuccessful.
         */
        public function UpdateContact(object $contact) : object|false
        {
            if ($this->mysql->connect_error != null)
                return false;
        
            $result = $this->mysql->query("UPDATE Contacts SET firstName='$contact->firstName', lastName='$contact->lastName', email='$contact->email', phone='$contact->phone' WHERE ID=$contact->ID");
            
            if ($result)
                return $this->GetContactByID($contact->ID);

            return false;
        }

        /**
         * Deletes contact record.
         * 
         * @param object $contact contact object of the Contact class.
         * @return bool true if operation was successful or false otherwise.
         */
        public function DeleteContact(object $contact) : bool
        {
            if ($this->mysql->connect_error != null)
                return false;
            
            if ($this->GetContactByID($contact->ID) == false)
                return false;

            $result = $this->mysql->query("DELETE FROM Contacts WHERE ID=$contact->ID");

            return $result;
        }
    }
?>
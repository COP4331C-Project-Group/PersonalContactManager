<?php
    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/../../images/model/ImageAPI.php';

    class ContactAPI 
    {
        private mysqli $mysql;
        private ImageAPI $imageAPI;

        public function __construct(mysqli $mysql, ImageAPI $imageAPI)
        {
            $this->mysql = $mysql;
            $this->imageAPI = $imageAPI;
        }

        /**
         * Creates contact record.
         * 
         * @param object $contact object of the Contact class.
         * @return object|false object of the Contact class containing all information about created record or false if operation was unsuccessful. 
         */
        public function CreateContact(object $contact) : object|false 
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $stmt = $this->mysql->prepare("INSERT INTO Contacts (ID, firstName, lastName, email, phone, userID, contactImageID) VALUES(DEFAULT, ?, ?, ?, ?, ?, NULL)");
            $stmt->bind_param(
                "ssssi", 
                $contact->firstName, 
                $contact->lastName, 
                $contact->email, 
                $contact->phone, 
                $contact->userID
            );

            $result = $stmt->execute();

            if ($result === false)
                return false;
            
            $contactRecord = $this->GetContactByID($this->mysql->insert_id);

            if ($contact->contactImage !== NULL && strlen($contact->contactImage->imageAsBase64) !== 0) {
                $contact->contactImage->setName(strval($contactRecord->ID));
                
                $image = $this->imageAPI->CreateImage($contact->contactImage);

                if ($image === false)
                    throw new RuntimeException("Can't create image");

                $contactRecord->contactImage = $image;

                $contactRecord = $this->UpdateContact($contactRecord);
            }

            return $contactRecord;
        }

        /**
         * Gets contact record by contact's unique identifier.
         * 
         * @param int $contactID unique contact identifier.
         * @return object|false object of the Contact class containing all information about record or false if operation was unsuccessful.
         */
        private function GetContactByID($contactID) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $result = $this->mysql->query("SELECT * FROM Contacts WHERE ID=$contactID");

            if ($result === false)
                return false;
        
            $record = $result->fetch_object();

            if ($record === null)
                return false;

            $contact = Contact::Deserialize($record);

            if ($record->contactImageID !== NULL)
            {
                $image = $this->imageAPI->GetImageByID($record->contactImageID);
                
                if ($image === false)
                    return false;

                $contact->contactImage = $image;
            }

            return $contact;
        }

        /**
         * Gets contact record which satisfies query.
         * 
         * @param string $query search query used for searching for set of records in the database.
         * @param int $numOfResults max number of results that satisfy search query to be returned if search is successful.
         * @param int $userID unique user identifier.
         * @return array|false array of objects of the Contact class containing all information about each individual record or false if operation was unsuccessful.
         */
        public function GetContact(string $query, int $userID, int $numOfResults) : array|false
        {
            if ($this->mysql->connect_error !== null)
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
        
            if ($result === false)
                return false;
        
            $resultArray = [];

            while($record = $result->fetch_object()) {
                $contact = Contact::Deserialize($record);

                if ($record->contactImageID !== NULL)
                {
                    $image = $this->imageAPI->GetImageByID($record->contactImageID);
                    $contact->contactImage = $image;
                }   

                $resultArray[] = $contact;
            }

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
            if ($this->mysql->connect_error !== null)
                return false;

            $existingContact = $this->GetContactByID($contact->ID);

            if ($existingContact === false)
                return false;

            if ($contact->contactImage !== NULL && strlen($contact->contactImage->imageAsBase64) > 0) {
                $image = $contact->contactImage->setName(strval($contact->ID));

                if ($existingContact->contactImage !== NULL)
                    $contact->contactImage = $this->imageAPI->UpdateImage($image->setID($existingContact->contactImage->ID));
                else
                    $contact->contactImage = $this->imageAPI->CreateImage($image);
            }
            else
            {
                if ($existingContact->contactImage !== NULL)
                    $this->imageAPI->DeleteImage($existingContact->contactImage);
            }

            $query = "UPDATE Contacts SET firstName='$contact->firstName', lastName='$contact->lastName', email='$contact->email', phone='$contact->phone', ";

            if ($contact->contactImage === NULL || strlen($contact->contactImage->imageAsBase64) === 0)
                $query = $query . "contactImageID=NULL WHERE ID=$contact->ID";
            else
                $query = $query . "contactImageID={$contact->contactImage->ID} WHERE ID=$contact->ID";
            
            $result = $this->mysql->query($query);

            if ($result !== false)
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
            if ($this->mysql->connect_error !== null)
                return false;

            $contact = $this->GetContactByID($contact->ID);
    
            if ($contact === false)
                return false;

            $this->imageAPI->DeleteImage($contact->contactImage);

            $result = $this->mysql->query("DELETE FROM Contacts WHERE ID=$contact->ID");

            return $result;
        }
    }
?>

<?php
    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/../../images/model/ImageAPI.php';
    require_once __DIR__ . '/../../server/ServerException.php';

    class ContactAPI 
    {
        private mysqli $mysql;
        private ImageAPI $imageAPI;
        private $columnNames;

        public function __construct(mysqli $mysql, ImageAPI $imageAPI)
        {
            $this->mysql = $mysql;
            $this->imageAPI = $imageAPI;
            $this->columnNames = $this->GetContactColumns();
        }

        /**
         * Creates contact record.
         * 
         * @param object $contact object of the Contact class.
         * @return object|false object of the Contact class containing all information about created record or false if operation was unsuccessful. 
         * @throws ServerException When image attached to contact is not valid || image doesn't exist || image cannot be coverted to GdImage || image cannot be converted to image file.
         */
        public function CreateContact(object $contact) : object|false 
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $stmt = $this->mysql->prepare("INSERT INTO Contacts (ID, firstName, lastName, email, phone, userID, contactImageID, dateCreated) VALUES(DEFAULT, ?, ?, ?, ?, ?, NULL, DEFAULT)");
            $stmt->bind_param(
                "ssssi", 
                $contact->firstName, 
                $contact->lastName, 
                $contact->email, 
                $contact->phone, 
                $contact->userID
            );

            $records = $stmt->execute();

            if ($records === false)
                return false;
            
            $contactRecord = $this->GetContactByID($this->mysql->insert_id);

            // Checks whether an image was assigned to the new contact
            // If so, tries to create an image and updates Contact table with the ID of that image
            if ($contact->contactImage !== NULL && strlen($contact->contactImage->imageAsBase64) > 0) {
                $contactRecord->contactImage = $contact->contactImage->setName(strval($contactRecord->ID));
                $contactRecord = $this->UpdateContact($contactRecord);
            }

            return $contactRecord;
        }

        /**
         * Gets contact record by contact's unique identifier.
         * 
         * @param int $contactID unique contact identifier.
         * @return object|false object of the Contact class containing all information about record or false if operation was unsuccessful.
         * @throws ServerException When image attached to contact is not valid || image doesn't exist.
         */
        private function GetContactByID($contactID) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $records = $this->mysql->query("SELECT * FROM Contacts WHERE ID=$contactID");

            if ($records === false)
                return false;
        
            $record = $records->fetch_object();

            if ($record === null)
                return false;

            $contact = Contact::Deserialize($record);

            // Checks whether image is assigned to the contact record
            // If so, tries to get that image and assign it to the contact object
            if ($record->contactImageID !== NULL)
            {
                $image = $this->imageAPI->GetImageByID($record->contactImageID);
                
                if ($image === false)
                    return false;

                $contact->contactImage = $image;
            }

            return $contact;
        }

        private function GetContactColumns() : array|false
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            $databaseName = $this->mysql->query("SELECT database() AS dbName")->fetch_object();

            $columnNames = $this->mysql->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$databaseName->dbName' AND TABLE_NAME = 'Contacts'")->fetch_all();
            $columnNames = array_map(function($item){return $item[0];}, $columnNames);

            return $columnNames;
        }

        /**
         * Gets contact record which satisfies query.
         * 
         * @param string $query search query used for searching for set of records in the database.
         * @param int $numOfRecords max number of records that satisfy search query to be returned if search is successful.
         * @param int $userID unique user identifier.
         * @return array|false array of objects of the Contact class containing all information about each individual record or false if operation was unsuccessful.
         * @throws ServerException When image attached to contact is not valid || image doesn't exist.
         */
        public function GetContact(string $query, int $userID, int $page, int $itemsPerPage) : array|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $columnNames = $this->columnNames;

            if (!$columnNames)
                return false;

            unset($columnNames[array_search("userID", $columnNames)]);
            unset($columnNames[array_search("dateCreated", $columnNames)]);
            unset($columnNames[array_search("ID", $columnNames)]);

            $columnNames = array_values($columnNames);

            $queryParameters = explode(" ", $query);
            $records = array();

            foreach($queryParameters as $parameter) {
                $recordsPerParameter = array();

                foreach ($columnNames as $columnName) 
                {
                    $results = $this->mysql->query("SELECT * FROM Contacts WHERE {$columnName} LIKE '%{$parameter}%' AND userID={$userID}")->fetch_all(MYSQLI_ASSOC);

                    if (!empty($results))
                        $recordsPerParameter = array_merge($recordsPerParameter, $results);
                }

                if (!empty($recordsPerParameter))
                    $records[] = $recordsPerParameter;
                else
                    return false;
            }

            $length = count($records);
            $intersectedRecords = $records[0];
            
            for ($i = 1; $i < $length; $i++) 
            {
                $cur = array();
                
                foreach($records[$i] as $record)
                {
                    foreach($intersectedRecords as $intersectedRecord)
                        if ($record["ID"] === $intersectedRecord["ID"])
                            $cur[] = $record;
                }

                if (empty($cur))
                    return false;
                    
                $intersectedRecords = $cur;
            }

            $intersectedRecords = array_unique($intersectedRecords, SORT_REGULAR);
            $contacts = array();
            
            foreach($intersectedRecords as $record) 
                $contacts[] = $this->GetContactByID($record["ID"]);

            return array_slice($contacts, $page * $itemsPerPage, $itemsPerPage);
        }

        /**
         * Updates contact record.
         * 
         * @param object $contact contact object of the Contact class.
         * @return object|false contact object of the Contact class containing updated information or false if operation was unsuccessful.
         * @throws ServerException When image attached to contact is not valid || image doesn't exist || image cannot be coverted to GdImage || image cannot be converted to image file.
         */
        public function UpdateContact(object $contact) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $existingContact = $this->GetContactByID($contact->ID);

            if ($existingContact === false)
                return false;
            
            /**
             * If new contact information contains image, then we are dealing with 2 cases:
             * 1st -> There is an existing image attached to the contact
             * 2nd -> There is no existing image attached to the contact
             * 
             * If Case 1 is valid, we need to "Update" existing image with the new image
             * If Case 2 is valid, we need to "Create" new image
             *  */ 
            if ($contact->contactImage !== NULL && strlen($contact->contactImage->imageAsBase64) > 0) {
                $image = $contact->contactImage->setName(strval($contact->ID));

                if ($existingContact->contactImage !== NULL)
                    $contact->contactImage = $this->imageAPI->UpdateImage($image->setID($existingContact->contactImage->ID));
                else
                    $contact->contactImage = $this->imageAPI->CreateImage($image);
            }
            /**
             * If new contact information doesn't contain image, then we are dealing with 2 cases:
             * 1st -> There is an existing image attached to the contact
             * 2nd -> There is no existing image attached to the contact
             * 
             * If Case 1 is valid, we nee to "Delete" existing image
             * If Case 2 is valid, we don't need to do anything
             */
            else
            {
                if ($existingContact->contactImage !== NULL)
                    $this->imageAPI->DeleteImage($existingContact->contactImage);
            }

            $query = "UPDATE Contacts SET firstName='$contact->firstName', lastName='$contact->lastName', email='$contact->email', phone='$contact->phone', ";

            /**
             *  mysqli->query call doesn't accept mixed datatype, hence we have to explicitly define two queries which cover two cases ->
             *  1st Case -> there is not contact image attached -> NULL
             *  2nd Case -> there is an image attached -> contactImageID
             */
            if ($contact->contactImage === NULL || strlen($contact->contactImage->imageAsBase64) === 0)
                $query = $query . "contactImageID=NULL WHERE ID=$contact->ID";
            else
                $query = $query . "contactImageID={$contact->contactImage->ID} WHERE ID=$contact->ID";
            
            $records = $this->mysql->query($query);

            if ($records !== false)
                return $this->GetContactByID($contact->ID);

            return false;
        }

        /**
         * Deletes contact record.
         * 
         * @param object $contact contact object of the Contact class.
         * @return bool true if operation was successful or false otherwise.
         * @throws ServerException When image attached to contact is not valid || image doesn't exist.
         */
        public function DeleteContact(object $contact) : bool
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $contact = $this->GetContactByID($contact->ID);
    
            if ($contact === false)
                return false;

            if ($contact->contactImage !== NULL)
                $this->imageAPI->DeleteImage($contact->contactImage);

            $records = $this->mysql->query("DELETE FROM Contacts WHERE ID=$contact->ID");

            return $records;
        }
    }
?>

<?php
    require_once __DIR__ . '/Contact.php';
    require_once __DIR__ . '/../../images/model/ImageAPI.php';
    require_once __DIR__ . '/../../server/ServerException.php';

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

            $queryParameters = explode(" ", $query);

            $queryArray = array();
            foreach($queryParameters as $parameter)
                $queryArray[] = "SELECT * FROM Contacts WHERE (firstName LIKE '%{$parameter}%' OR lastName LIKE '%{$parameter}%' OR phone LIKE '%{$parameter}%' OR email LIKE '%{$parameter}%') AND userID={$userID}";

            $recordArray = array();
            foreach($queryArray as $query) {
                $records = $this->mysql->query($query)->fetch_all(MYSQLI_ASSOC);

                if ($records !== null)
                    $recordArray[] = $records;
            }

            if (empty($recordArray))
                return false;


            $recordArray = call_user_func_array('array_intersect_key', $recordArray);

            if ($recordArray === false)
                return false;

            $contacts = array();

            foreach($recordArray as $record) {
                $contact = Contact::Deserialize((object) $record);

                // Checks whether image is assigned to the contact record
                // If so, tries to get that image and assign it to the contact object
                if ($record->contactImageID !== NULL)
                {
                    $image = $this->imageAPI->GetImageByID($record->contactImageID);
                    $contact->contactImage = $image;
                }   

                $contacts[] = $contact;
            }

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

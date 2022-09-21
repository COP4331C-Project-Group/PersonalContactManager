<?php
    require_once __DIR__ . '/Image.php';
    require_once __DIR__ . '/../../server/Server.php';
    require_once __DIR__ . '/../../server/ServerException.php';

    class ImageAPI
    {
        private mysqli $mysql;
        private Server $server;

        public function __construct(mysqli $mysql)
        {
            $this->server = new Server();
            $this->mysql = $mysql;
        }

        /**
         * @throws ServerException When image is not valid || image doesn't exist.
         */
        public function CreateImage(object $image) : object|false 
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            $this->server->SaveImage($image);

            $stmt = $this->mysql->prepare("INSERT INTO Images (ID, name, extension) VALUES (DEFAULT, ?, ?)");
            $stmt->bind_param(
                "ss",
                $image->name,
                $image->extension
            );

            $result = $stmt->execute();

            if ($result !== false)
                return $this->GetImageByID($this->mysql->insert_id);

            return false;
        }

        /**
         * @throws ServerException When image is not valid || image doesn't exist.
         */
        private function GetImageBySQLQuery(string $query) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $result = $this->mysql->query($query);

            if ($result === false)
                return false;
            
            $record = $result->fetch_object();

            if ($record === null)
                return false;

            $image = Image::Deserialize($record);

            $this->server->LoadImage($image);
            
            return $image;
        } 

        /**
         * @throws ServerException When image is not valid || image doesn't exist.
         */
        public function GetImageByID(int $imageID) : object|false
        {
            return $this->GetImageBySQLQuery("SELECT * FROM Images WHERE ID=$imageID");
        }

        /**
         * @throws ServerException When image is not valid || image doesn't exist.
         */
        public function GetImageByName(string $imageName) : object|false
        {
            return $this->GetImageBySQLQuery("SELECT * FROM Images WHERE name='$imageName'");
        }

        /**
         * @throws ServerException When image is not valid || image doesn't exist || image cannot be coverted to GdImage || image cannot be converted to image file.
         */
        public function UpdateImage(object $image) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

            $cachedImage = $this->server->ImageExists($image) ? clone $image : NULL;            

            if ($cachedImage !== NULL)
            {
                $this->server->LoadImage($cachedImage);
                $this->server->DeleteImage($cachedImage);
            }

            try 
            {
                $this->server->SaveImage($image);
            }
            catch (ServerException $e)
            {
                if ($cachedImage !== NULL)
                    $this->server->SaveImage($cachedImage);

                throw new ServerException($e->getMessage());
            }

            $result = $this->mysql->query("UPDATE Images SET name='$image->name', extension='$image->extension' WHERE ID='$image->ID'");

            if ($result !== false)
                return $this->GetImageByID($image->ID);
            
            return false;
        }

        /**
         * @throws ServerException When image is not valid || image doesn't exist.
         */
        public function DeleteImage(object $image) : bool
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            if ($this->GetImageByID($image->ID) == false)
                return false;
            
            $this->server->DeleteImage($image);

            $result = $this->mysql->query("DELETE FROM Images WHERE ID=$image->ID");

            return $result;
        }
    }
?>

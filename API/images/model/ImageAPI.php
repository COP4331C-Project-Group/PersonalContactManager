<?php
    require_once __DIR__ . '/Image.php';
    require_once __DIR__ . '/../../server/Server.php';

    class ImageAPI
    {
        private mysqli $mysql;
        private Server $server;

        public function __construct(mysqli $mysql)
        {
            $this->server = new Server();
            $this->mysql = $mysql;
        }

        public function CreateImage(object $image) : object|false 
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            if (!$this->server->SaveImage($image))
                throw new RuntimeException("Can't save image");

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

            if (!$this->server->LoadImage($image))
                throw new RuntimeException("Can't load image");
            
            return $image;
        } 

        public function GetImageByID(int $imageID) : object|false
        {
            return $this->GetImageBySQLQuery("SELECT * FROM Images WHERE ID=$imageID");
        }

        public function GetImageByName(string $imageName) : object|false
        {
            return $this->GetImageBySQLQuery("SELECT * FROM Images WHERE name='$imageName'");
        }

        public function UpdateImage(object $image) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            if (!$this->server->DeleteImage($image))
                throw new RuntimeException("Can't delete image");
            
            if (!$this->server->SaveImage($image))
                throw new RuntimeException("Can't save image");

            $result = $this->mysql->query("UPDATE Images SET name='$image->name', extension='$image->extension' WHERE ID='$image->ID'");

            if ($result !== false)
                return $this->GetImageByID($image->ID);
            
            return false;
        }

        public function DeleteImage(object $image) : bool
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            if ($this->GetImageByID($image->ID) == false)
                return false;
            
            if (!$this->server->DeleteImage($image))
                throw new RuntimeException("Can't delete image");

            $result = $this->mysql->query("DELETE FROM Images WHERE ID=$image->ID");

            return $result;
        }
    }
?>

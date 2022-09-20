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
            
            $stmt = $this->mysql->prepare("INSERT INTO Images (ID, name, extension) VALUES (DEFAULT, ?, ?)");
            $stmt->bind_param(
                "ss",
                $image->name,
                $image->extension
            );

            if (!$this->server->SaveImage($image))
                return false;

            $result = $stmt->execute();

            if ($result !== false)
                return $this->GetImageByID($this->mysql->insert_id);

            return false;
        }

        public function GetImageByID(int $imageID) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;
            
            $result = $this->mysql->query("SELECT * FROM Images WHERE ID=$imageID");

            if ($result === false)
                return false;
            
            $record = $result->fetch_object();

            if ($record === null)
                return false;

            $image = Image::Deserialize($record);

            if (!$this->server->LoadImage($image))
                return false;
            
            return $image;
        }

        public function UpdateImage(object $image) : object|false
        {
            if ($this->mysql->connect_error !== null)
                return false;

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
                return false;

            $result = $this->mysql->query("DELETE FROM Images WHERE ID=$image->ID");

            return $result;
        }
    }
?>

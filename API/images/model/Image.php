<?php
    require_once __DIR__ . '/../../JSONObject.php';

    class Image extends JSONObject
    {
        public int $ID;
        public string $name;
        public string $extension;

        public string $imageAsBase64;

        public function __construct()
        {
            $this->ID = -1;
            $this->name = "";
            $this->extension = "";
            $this->imageAsBase64 = "";
        }

        public static function create (
            string $extension) : Image
        {
            $instance = new self();
            $instance->extension = $extension;

            return $instance;
        }

        public function setImageAsBase64(string $imageAsBase64) : Image
        {
            $this->imageAsBase64 = $imageAsBase64;
            return $this;
        }

        public function setID(int $imageID) : Image
        {
            $this->ID = $imageID;
            return $this;
        }

        public function setName(string $name) : Image
        {
            $this->name = $name;
            return $this;
        }

        public function setExtension(string $extension) : Image
        {
            $this->extension = $extension;
            return $this;
        }

        public function jsonSerialize(): mixed
        {
            return [
                "ID" => $this->ID,
                "name" => $this->name,
                "extension" => $this->extension,
                "imageAsBase64" => $this->imageAsBase64 
            ];
        }
    }
?>
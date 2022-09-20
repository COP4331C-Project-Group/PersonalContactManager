<?php
    require_once __DIR__ . '/../images/model/Image.php';

    class Server
    {
        private string $imagePath;

        public function __construct()
        {
            $this->imagePath = getenv("HTTP_IMAGE_PATH");
        }

        public function GetImage(Image &$image) : bool
        {
            $fullPath = $this->imagePath . $image->name . $image->extension;

            $file = file_get_contents($fullPath, FILE_USE_INCLUDE_PATH);

            if ($file === false)
                return false;
            
            $image->imageAsBase64 = base64_encode($file);

            return true;
        }

        public function DeleteImage(Image $image) : bool
        {
            $fullPath = $this->imagePath . $image->name . $image->extension;

            return unlink($fullPath);
        }
    }
?>

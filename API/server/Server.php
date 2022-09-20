<?php
    require_once __DIR__ . '/../images/model/Image.php';

    class Server
    {
        private string $imagePath;

        public function __construct()
        {
            $this->imagePath = getenv("HTTP_IMAGE_PATH");
        }

        public function SaveImage(Image $image) : bool
        {
            $binary = base64_decode($image->imageAsBase64);

            $gdImage = imagecreatefromstring($binary);

            if (!$image)
                die("imageAsBase64 is not valid");
            
            $fullPath = $this->imagePath . $image->name . "." . $image->extension;

            return imagepng($gdImage, $fullPath, 0);
        }

        public function LoadImage(Image &$image) : bool
        {
            $fullPath = $this->imagePath . $image->name . "." . $image->extension;
            
            $file = file_get_contents($fullPath, FILE_USE_INCLUDE_PATH);

            if ($file === false)
                return false;
            
            $image->imageAsBase64 = base64_encode($file);

            return true;
        }

        public function DeleteImage(Image $image) : bool
        {
            $fullPath = $this->imagePath . $image->name . "." . $image->extension;

            return unlink($fullPath);
        }
    }
?>

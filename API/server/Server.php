<?php
    require_once __DIR__ . '/../images/model/Image.php';
    require_once __DIR__ . '/ServerException.php';

    class Server
    {
        private string $imagePath;

        public function __construct()
        {
            $this->imagePath = getenv("HTTP_IMAGE_PATH");
        }

        /**
         * @throws ServerException When image is not valid.
         */
        private function ValidateImageOrThrow(Image $image)
        {
            if (strlen($image->extension) == 0)
                throw new ServerException("Image doesn't have an extension.");

            if (strlen($image->name) == 0)
                throw new ServerException("Image doesn't have a name.");
        }

        /**
         * @throws ServerException When image is not valid || image directory is not found.
         */
        public function ImageExists(Image $image) : bool
        {
            $this->ValidateImageOrThrow($image);

            $fullName = $image->name . "." . $image->extension;

            $dir = scandir($this->imagePath);

            if ($dir === false)
                throw new ServerException("Directory {$this->imagePath} doesn't exist.");
            
            foreach ($dir as $fileName) {
                if (strcmp($fileName, $fullName) == 0)
                    return true;
            }

            return false;
        }

        /**
         * @throws ServerException When image is not valid || image cannot be coverted to GdImage || image cannot be converted to image file.
         */
        public function SaveImage(Image $image) : bool
        {
            $this->ValidateImageOrThrow($image);

            $binary = base64_decode($image->imageAsBase64);

            $gdImage = imagecreatefromstring($binary);

            if (!$image)
                throw new ServerException("Image {$image->name}.{$image->extension} cannot be converted from Base64 string to GdImage object");
            
            $fullPath = $this->imagePath . $image->name . "." . $image->extension;
            
            try 
            {
                return imagepng($gdImage, $fullPath, 0);
            }
            catch (TypeError $e)
            {
                throw new ServerException("Image {$image->name}.{$image->extension} cannot be converted from GdImage object to png.");
            }
        }
        /**
         * @throws ServerException When image is not valid || image doesn't exist.
         */
        public function LoadImage(Image &$image) : bool
        {
            $this->ValidateImageOrThrow($image);

            if (!$this->ImageExists($image))
                throw new ServerException("Image {$image->name}.{$image->extension} doesn't exist in {$this->imagePath} directory.");

            $fullPath = $this->imagePath . $image->name . "." . $image->extension;
            
            $file = file_get_contents($fullPath, FILE_USE_INCLUDE_PATH);

            if ($file === false)
                return false;
            
            $image->imageAsBase64 = base64_encode($file);

            return true;
        }

        /**
         * @throws ServerException When image is not valid || image doesn't exist.
         */
        public function DeleteImage(Image $image) : bool
        {
            $this->ValidateImageOrThrow($image);

            if (!$this->ImageExists($image))
                throw new ServerException("Image {$image->name}.{$image->extension} doesn't exist in {$this->imagePath} directory.");

            $fullPath = $this->imagePath . $image->name . "." . $image->extension;

            return unlink($fullPath);
        }
    }
?>

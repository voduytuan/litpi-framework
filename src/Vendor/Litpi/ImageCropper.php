<?php

namespace Vendor\Litpi;

class ImageCropper
{
    public $cropPass = false;

    //You do not need to alter these functions
    public function __construct($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);

        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
            case "image/gif":
                $source=imagecreatefromgif($image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source=imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source=imagecreatefrompng($image);
                break;
        }

        imagecopyresampled(
            $newImage,
            $source,
            0,
            0,
            $start_width,
            $start_height,
            $newImageWidth,
            $newImageHeight,
            $width,
            $height
        );

        switch ($imageType) {
            case "image/gif":
                $this->cropPass = imagegif($newImage, $thumb_image_name);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $this->cropPass = imagejpeg($newImage, $thumb_image_name, 90);
                break;
            case "image/png":
            case "image/x-png":
                $this->cropPass = imagepng($newImage, $thumb_image_name);
                break;
        }
    }
}

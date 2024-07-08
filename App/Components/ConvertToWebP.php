<?php

namespace Components;

class ConvertToWebP 
{
    public function convertToWebP($source, $destination, $productSlug, $categoryId, $quality = 80)
    {
        $image = imagecreatefromstring(file_get_contents($source));
        if ($image !== false) 
        {
            $webpImagePath = $destination . $productSlug . '-' . $categoryId . '.webp';

            if (imagewebp($image, $webpImagePath, $quality)) 
            {
                imagedestroy($image);
                unlink($source);
                return $webpImagePath;
            } 
            else 
            {
                imagedestroy($image);
                return false;
            }
        } 
        else 
        {
            return false;
        }
    }
}

?>

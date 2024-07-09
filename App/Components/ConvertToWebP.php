<?php
namespace Components;

class ConvertToWebP 
{
    public function convertToWebP($source, $destination, $productSlug, $categoryId, $quality = 80)
    {
        $image = imagecreatefromstring(file_get_contents($source));
        if ($image !== false) 
        {

            $finalImage = $this->resizeAndCropImage($image, 900, 1200);
            if ($finalImage === false) {
                return false;
            }

            $webpImagePath = $destination . $productSlug . '-' . $categoryId . '.webp';

            if (imagewebp($finalImage, $webpImagePath, $quality)) 
            {
                imagedestroy($image);
                imagedestroy($finalImage);
                unlink($source);
                return $webpImagePath;
            } 
            else 
            {
                imagedestroy($image);
                imagedestroy($finalImage);
                return false;
            }
        } 
        else 
        {
            return false;
        }
    }

    private function resizeAndCropImage($image, $targetWidth, $targetHeight)
    {
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calcul du ratio
        $ratio = max($targetWidth / $originalWidth, $targetHeight / $originalHeight);

        // Nouvelle largeur et hauteur en gardant le ratio
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);

        // Création de la nouvelle image redimensionnée
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($resizedImage === false) {
            return false;
        }

        // Garder la transparence si l'image originale a un canal alpha
        if (imagealphablending($resizedImage, false)) {
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefill($resizedImage, 0, 0, $transparent);
        }

        if (!imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
            imagedestroy($resizedImage);
            return false;
        }

        // Calcul des positions de départ pour centrer l'image redimensionnée
        $xOffset = ($newWidth - $targetWidth) / 2;
        $yOffset = ($newHeight - $targetHeight) / 2;

        // Création de l'image finale rognée
        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($finalImage === false) {
            return false;
        }

        if (imagealphablending($finalImage, false)) {
            imagesavealpha($finalImage, true);
            $transparent = imagecolorallocatealpha($finalImage, 0, 0, 0, 127);
            imagefill($finalImage, 0, 0, $transparent);
        }

        if (!imagecopy($finalImage, $resizedImage, 0, 0, $xOffset, $yOffset, $targetWidth, $targetHeight)) {
            imagedestroy($resizedImage);
            imagedestroy($finalImage);
            return false;
        }

        imagedestroy($resizedImage);
        return $finalImage;
    }
}


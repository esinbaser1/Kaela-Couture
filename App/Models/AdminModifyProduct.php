<?php

namespace Models;

use App\Database;
use Lib\Slug;

class AdminModifyProduct
{
    protected $db;
    protected $slug;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    public function getProductById($productId)
    {
        try {
            $request = "SELECT * FROM image WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $product = $pdo->fetch();

            if ($product) {
                return ["success" => true, "product" => $product];
            } else {
                return ["success" => false, "message" => "Product not found"];
            }
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when fetching product: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    public function updateProduct()
    {
        $productId = $_POST['productId'] ?? null;

        if ($productId) 
        {
            $productName = $_POST['productName'] ?? '';
            $productDescription = $_POST['productDescription'] ?? '';
            $productCategory = $_POST['productCategory'] ?? '';
            $productImage = $_FILES['productImage'] ?? null;
    
            try 
            {
                $productSlug = $this->slug->sluguer($productName);
    
                if ($productImage) 
                {
                    $imageLocation = "assets/img/";
                    $tempImagePath = $imageLocation . basename($productImage['name']);
    
                    if (!move_uploaded_file($productImage['tmp_name'], $tempImagePath)) 
                    {
                        return ["success" => false, "message" => "Failed to move uploaded file"];
                    }
    
                    $webpImagePath = $this->convertToWebP($tempImagePath, $imageLocation, $productSlug, $productCategory);
    
                    if (!$webpImagePath) 
                    {
                        return ["success" => false, "message" => "Failed to convert image to WebP format"];
                    }
    
                    $newWebpFileName = $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
                    $newWebpImagePath = $imageLocation . $newWebpFileName;
    
                    if (!rename($webpImagePath, $newWebpImagePath)) 
                    {
                        return ["success" => false, "message" => "Failed to rename WebP image"];
                    }
    
                    $imagePath = $newWebpFileName;
                } 
                else 
                {
                    //ancien chemin d'image si aucune nouvelle image n'est téléchargée
                    $request = "SELECT path FROM image WHERE id = ?";
                    $pdo = $this->db->prepare($request);
                    $pdo->execute([$productId]);
                    $existingProduct = $pdo->fetch();
                    $imagePath = $existingProduct['path'];  //l'image existante est utilisée
                }
    
                $request = "UPDATE image SET name = ?, description = ?, path = ?, slug = ?, categorie_id = ? WHERE id = ?";
                $pdo = $this->db->prepare($request);
                $pdo->execute([$productName, $productDescription, $imagePath, $productSlug, $productCategory, $productId]);
    
                return ["success" => true, "message" => "Product updated successfully"];
                
            } 
            catch (\PDOException $e) 
            {
                error_log("Error when updating product: " . $e->getMessage());
                return ["success" => false, "message" => "Database error: " . $e->getMessage()];
            }
        } 
        else 
        {
            return ["success" => false, "message" => "Product ID missing"];
        }
    }
    private function convertToWebP($source, $destination, $productSlug, $categoryId, $quality = 80)
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

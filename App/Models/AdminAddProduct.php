<?php

namespace Models;

use App\Database;
use Lib\Slug;

class AdminAddProduct 
{
    protected $db;
    protected $slug;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    public function addProduct()
{
    $productName = isset($_POST['productName']) ? strip_tags($_POST['productName']) : null;
    $productDescription = isset($_POST['productDescription']) ? strip_tags($_POST['productDescription']) : null;
    $productCategory = isset($_POST['productCategory']) ? $_POST['productCategory'] : null;
    $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;
    $productSlug = $this->slug->sluguer($productName);

    if (empty($productName) || empty($productDescription) || empty($productCategory) || empty($productImage)) 
    {
        return ["success" => false, "message" => "All fields are required"];
    }

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

    try 
    {
        $request = "INSERT INTO image (name, description, path, slug, categorie_id) VALUES (?,?,?,?,?)";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$productName, $productDescription, $webpImagePath, $productSlug, $productCategory]);

        $productId = $this->db->lastInsertId();

        $newWebpFileName =  $productSlug . '-' . $productId . '-' . $productCategory . '.webp';

        $newWebpImagePath = $imageLocation . $newWebpFileName;

        if (!rename($webpImagePath, $newWebpImagePath)) 
        {
            return ["success" => false, "message" => "Failed to rename WebP image"];
        }
 
        $request = "UPDATE image SET path = ? WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$newWebpFileName, $productId]);

        return ["success" => true, "message" => "Product added successfully"];
    } 

    catch (\PDOException $e) 
    {
        error_log("Error when creating product: " . $e->getMessage());
        return ["success" => false, "message" => "Database error"];
    }
}
    
    public function getCategorie()

    {
        try 
        {
            $request = "SELECT id, name FROM categorie";
            $pdo = $this->db->query($request);
            $categories = $pdo->fetchAll();

            return ["success" => true, "categories" => $categories];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when retrieving categories: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    private function convertToWebP($source, $destination, $productSlug, $categoryId, $quality = 80) 
    {
        $image = imagecreatefromstring(file_get_contents($source));
        if ($image !== false) {
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

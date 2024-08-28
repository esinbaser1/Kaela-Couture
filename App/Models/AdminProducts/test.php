<?php

namespace AdminProducts;

use App\Database;
use Lib\Slug;
use Components\ConvertToWebP;

class AdminUpdateProduct
{
    protected $db;
    protected $slug;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    private function deleteOldImage($imagePath) {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    public function getProductById()
    {
        $productId = isset($_GET['productId']) ? strip_tags($_GET['productId']) : null;

        if (!$productId) {
            return ["success" => false, "message" => "Product ID missing"];
        }

        try {
            $request = "SELECT * FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $productById = $pdo->fetch();

            if ($productById) {
                return ["success" => true, "product" => $productById];
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
        $productId = isset($_POST['productId']) ? strip_tags($_POST['productId']) : null;

        if ($productId) 
        {
            $productName = $_POST['productName'] ?? '';
            $productDescription = $_POST['productDescription'] ?? '';
            $productCategory = $_POST['productCategory'] ?? '';
            $productSection = $_POST['productSection'] ?? '';
            $productImage = $_FILES['productImage'] ?? null;


            // Fetch the current product data
            $request = "SELECT * FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $existingProduct = $pdo->fetch();
    
            // Check if the product exists
            if (!$existingProduct) {
                return ["success" => false, "message" => "Product not found"];
            }

            if ($productName === $existingProduct['name'] &&
            $productDescription === $existingProduct['description'] &&
            $productCategory == $existingProduct['categorie_id'] &&
            $productSection == $existingProduct['section_id'] &&
            !$productImage) {
            return ["success" => true, "message" => "No changes detected"];
        }

        if ($this->nameExist($productName) && $productName !== $existingProduct['name']) {
            return ["success" => false, "message" => "This name is already used"];
        }
    
            try 
            {
                $productSlug = $this->slug->sluguer($productName);
                $imagePath = '';

                if ($productImage) 
                {
                    $request = "SELECT path FROM product WHERE id = ?";
                    $pdo = $this->db->prepare($request);
                    $pdo->execute([$productId]);
                    $existingProduct = $pdo->fetch();
                    
                    if ($existingProduct) {
                        $this->deleteOldImage("assets/img/" . $existingProduct['path']);
                    }

                    $imageLocation = "assets/img/";
                    $tempImagePath = $imageLocation . basename($productImage['name']);
    
                    if (!move_uploaded_file($productImage['tmp_name'], $tempImagePath)) 
                    {
                        return ["success" => false, "message" => "Failed to move uploaded file"];
                    }
    
                    $converter = new ConvertToWebP();
                    $webpImagePath = $converter->convertToWebP($tempImagePath, $imageLocation, $productSlug, $productCategory);
    
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
                    $request = "SELECT path FROM product WHERE id = ?";
                    $pdo = $this->db->prepare($request);
                    $pdo->execute([$productId]);
                    $existingProduct = $pdo->fetch();
                    $imagePath = $existingProduct['path'];
                }
    
                $request = "UPDATE product SET name = ?, description = ?, path = ?, slug = ?, categorie_id = ?, section_id = ? WHERE id = ?";
                
                $pdo = $this->db->prepare($request);
                $pdo->execute([$productName, $productDescription, $imagePath, $productSlug, $productCategory, $productSection, $productId]);

    
                $request = "SELECT * FROM product WHERE id = ?";
                $pdo = $this->db->prepare($request);
                $pdo->execute([$productId]);
                $updatedProduct = $pdo->fetch();
    
                return ["success" => true, "message" => "Product updated successfully", "product" => $updatedProduct];
                
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

    private function nameExist($productName) {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM product WHERE name = ?");
        $pdo->execute([$productName]);
        return $pdo->fetchColumn() > 0;
    }
}
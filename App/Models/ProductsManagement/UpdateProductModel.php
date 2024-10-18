<?php

namespace Models\ProductsManagement;

use App\Database;
use Lib\Slug;
use Utils\ConvertToWebP;

// Class responsible for updating existing products in the admin panel
class UpdateProductModel
{
    protected $db;
    protected $slug;

    // Constructor: Initializes the database connection and Slug utility
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    // Private method to delete the old image file from the file system
    private function deleteOldImage($imagePath)
    {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Method to retrieve a product by its ID
    public function getProduct($productId)
    {
        try {
            // SQL query to select the product by ID
            $request = "SELECT * FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            return $pdo->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Throw exception if a database error occurs
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    // Method to update a product
    public function updateProduct($productId, $productName, $productDescription, $productCategory, $productSection, $productImage = null)
    {
        try {
            $productSlug = $this->slug->sluguer($productName);
            $existingProduct = $this->getProduct($productId);

            // If a new image is uploaded
            if ($productImage) {
                $this->deleteOldImage("assets/img/" . $existingProduct['path']);

                $imageLocation = "assets/img/";
                $tempImagePath = $imageLocation . basename($productImage['name']);

                if (!move_uploaded_file($productImage['tmp_name'], $tempImagePath)) {
                    throw new \Exception("Failed to move uploaded file");
                }

                // Convert and rename the new image
                $converter = new ConvertToWebP();
                $webpImagePath = $converter->convertToWebP($tempImagePath, $imageLocation, $productSlug, $productCategory);

                if (!$webpImagePath) {
                    throw new \Exception("Failed to convert image to WebP format");
                }

                $newWebpFileName = $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
                $newWebpImagePath = $imageLocation . $newWebpFileName;

                if (!rename($webpImagePath, $newWebpImagePath)) {
                    throw new \Exception("Failed to rename WebP image");
                }

                $imagePath = $newWebpFileName;
            } else {
                $imagePath = $existingProduct['path'];
            }

            // Update the product in the database
            $request = "UPDATE product SET name = ?, description = ?, path = ?, slug = ?, categorie_id = ?, section_id = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productName, $productDescription, $imagePath, $productSlug, $productCategory, $productSection, $productId]);

            return $this->getProduct($productId);
        } catch (\PDOException $e) {
            // Log any database errors and throw exception
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    // Private method to check if the product name already exists for another product
    public function nameExist($productName, $productId)
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM product WHERE name = ? AND id != ?");
        $pdo->execute([$productName, $productId]);
        return $pdo->fetchColumn() > 0;
    }
}
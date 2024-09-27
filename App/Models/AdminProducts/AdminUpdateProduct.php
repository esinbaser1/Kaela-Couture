<?php

namespace AdminProducts;

use App\Database;
use Lib\Slug;
use Components\ConvertToWebP;

// Class responsible for handling the update of existing products in the admin panel
class AdminUpdateProduct
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
    public function getProductById()
    {
        $productId = isset($_GET['productId']) ? strip_tags($_GET['productId']) : null;

        // If the product ID is missing, return an error message
        if (!$productId) {
            return ["success" => false, "message" => "Product ID missing"];
        }

        try {
            // SQL query to select the product by ID
            $request = "SELECT * FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $productById = $pdo->fetch(\PDO::FETCH_ASSOC);

            // If the product is found, return it; otherwise, return an error
            if ($productById) {
                return ["success" => true, "product" => $productById];
            } else {
                return ["success" => false, "message" => "Product not found"];
            }
        } catch (\PDOException $e) {
            // Return a failure message
            return ["success" => false, "message" => "Database error"];
        }
    }

    // Method to update the product
    public function updateProduct()
    {
        // Retrieve the product ID from the POST request
        $productId = isset($_POST['productId']) ? strip_tags($_POST['productId']) : null;

        // If a product ID is provided
        if ($productId) {
            // Retrieve and sanitize the input fields
            $productName = $_POST['productName'] ? trim(strip_tags($_POST['productName'])) : null;
            $productDescription = $_POST['productDescription'] ? trim(strip_tags($_POST['productDescription'])) : null;
            $productCategory = $_POST['productCategory'] ? strip_tags($_POST['productCategory']) : null;
            $productSection = $_POST['productSection'] ? strip_tags($_POST['productSection']) : null;
            $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;

            // Ensure all required fields are filled
            if (empty($productName) || empty($productDescription) || empty($productCategory) || empty($productSection)) {
                return ["success" => false, "message" => "All fields must be filled"];
            }

            // Retrieve the current product data from the database
            $request = "SELECT * FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $existingProduct = $pdo->fetch(\PDO::FETCH_ASSOC);

            // Check if any changes were made to the product data
            if (
                $productName == $existingProduct['name'] &&
                $productDescription == $existingProduct['description'] &&
                $productCategory == $existingProduct['categorie_id'] &&
                $productSection == $existingProduct['section_id'] &&
                !$productImage
            ) {
                return ["success" => false, "message" => "No changes detected"];
            }

            // Check if the new product name already exists
            if ($this->nameExist($productName, $productId)) {
                return ["success" => false, "message" => "This name is already used"];
            }

            try {
                $productSlug = $this->slug->sluguer($productName); // Create a slug for the product name
                $imagePath = '';

                // If a new product image is uploaded
                if ($productImage) {
                    // Delete the old image file
                    if ($existingProduct) {
                        $this->deleteOldImage("assets/img/" . $existingProduct['path']);
                    }

                    // Move the new image file to the correct location
                    $imageLocation = "assets/img/";
                    $tempImagePath = $imageLocation . basename($productImage['name']);

                    if (!move_uploaded_file($productImage['tmp_name'], $tempImagePath)) {
                        return ["success" => false, "message" => "Failed to move uploaded file"];
                    }

                    // Convert the new image to WebP format
                    $converter = new ConvertToWebP();
                    $webpImagePath = $converter->convertToWebP($tempImagePath, $imageLocation, $productSlug, $productCategory);

                    if (!$webpImagePath) {
                        return ["success" => false, "message" => "Failed to convert image to WebP format"];
                    }

                    // Rename the WebP image file and update the image path
                    $newWebpFileName = $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
                    $newWebpImagePath = $imageLocation . $newWebpFileName;

                    if (!rename($webpImagePath, $newWebpImagePath)) {
                        return ["success" => false, "message" => "Failed to rename WebP image"];
                    }

                    $imagePath = $newWebpFileName;
                } else {
                    // If no new image is uploaded, retain the existing image path
                    $imagePath = $existingProduct['path'];
                }

                // SQL query to update the product in the database
                $request = "UPDATE product SET name = ?, description = ?, path = ?, slug = ?, categorie_id = ?, section_id = ? WHERE id = ?";
                $pdo = $this->db->prepare($request);
                $pdo->execute([$productName, $productDescription, $imagePath, $productSlug, $productCategory, $productSection, $productId]);

                // Retrieve the updated product data
                $request = "SELECT * FROM product WHERE id = ?";
                $pdo = $this->db->prepare($request);
                $pdo->execute([$productId]);
                $updatedProduct = $pdo->fetch(\PDO::FETCH_ASSOC);

                // Return success response with the updated product data
                return ["success" => true, "message" => "Product updated successfully", "product" => $updatedProduct];
            } catch (\PDOException $e) {
                // Log any database errors and return a failure message
                error_log("Error when updating product: " . $e->getMessage());
                return ["success" => false, "message" => "Database error: " . $e->getMessage()];
            }
        } else {
            // If the product ID is missing, return an error message
            return ["success" => false, "message" => "Product ID missing"];
        }
    }

    // Private method to check if the product name already exists, excluding the current product being updated
    private function nameExist($productName, $productId = null)
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM product WHERE name = ? AND id != ?");
        $pdo->execute([$productName, $productId]);
        return $pdo->fetchColumn() > 0; // Returns true if the product name exists for another product
    }
}

<?php

namespace ProductsManagement;

use App\Database;
use Lib\Slug;
use Utils\ConvertToWebP;

// Class to handle adding new products in the admin panel
class AddProductModel
{
    protected $db;
    protected $slug;

    // Initializes the database connection and Slug utility
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    // Method to add a new product to the database
    public function addProduct()
    {
        // Retrieve and sanitize input fields from the POST request
        $productName = isset($_POST['productName']) ? trim(strip_tags($_POST['productName'])) : null;
        $productDescription = isset($_POST['productDescription']) ? trim(strip_tags($_POST['productDescription'])) : null;
        $productCategory = isset($_POST['productCategory']) ? strip_tags($_POST['productCategory']) : null;
        $productSection = isset($_POST['productSection']) ? strip_tags($_POST['productSection']) : null;
        $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;

        // Check if any required fields are missing
        if (empty($productName) || empty($productDescription) || empty($productCategory) || empty($productImage) || empty($productSection)) 
        {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        // Check if the product name already exists in the database
        if ($this->nameExist($productName)) 
        {
           return ["success" => false, "message" => "This name is already used"];
        }

        // Create a slug for the product name
        $productSlug = $this->slug->sluguer($productName);
        $imageLocation = "assets/img/"; // Define the location for storing images

        // Temporary path for the uploaded image
        $tempImagePath = $imageLocation . basename($productImage['name']);

        // Move the uploaded file to the temporary path
        if (!move_uploaded_file($productImage['tmp_name'], $tempImagePath)) 
        {
            return ["success" => false, "message" => "Failed to move uploaded file"];
        }

        // Convert the image to WebP format
        $converter = new ConvertToWebP();
        $webpImagePath = $converter->convertToWebP($tempImagePath, $imageLocation, $productSlug, $productCategory);

        // Check if the WebP conversion was successful
        if (!$webpImagePath) 
        {
            return ["success" => false, "message" => "Failed to convert image to WebP format"];
        }

        try 
        {
            // Insert the new product into the database
            $request = "INSERT INTO product (name, description, path, slug, categorie_id, section_id) VALUES (?,?,?,?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productName, $productDescription, $webpImagePath, $productSlug, $productCategory, $productSection]);

            // Get the ID of the newly inserted product
            $productId = $this->db->lastInsertId();

            // Rename the WebP file to include the product ID and category
            $newWebpFileName = $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
            $newWebpImagePath = $imageLocation . $newWebpFileName;

            // Rename the WebP file
            if (!rename($webpImagePath, $newWebpImagePath)) 
            {
                return ["success" => false, "message" => "Failed to rename WebP image"];
            }

            // Update the product path in the database with the new WebP file name
            $request = "UPDATE product SET path = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$newWebpFileName, $productId]);

            // Prepare the new product data for the response
            $newProduct = [
                'id' => $productId,
                'name' => $productName,
                'description' => $productDescription,
                'path' => $newWebpFileName,
                'slug' => $productSlug,
                'category_id' => $productCategory,
                'section_id' => $productSection
            ];

            // Return a success response with the new product data
            return ["success" => true, "message" => "Product added successfully", "product" => $newProduct];
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }

    // Private method to check if the product name already exists
    private function nameExist($productName) 
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM product WHERE name = ?");
        $pdo->execute([$productName]);
        return $pdo->fetchColumn() > 0;
    }
}

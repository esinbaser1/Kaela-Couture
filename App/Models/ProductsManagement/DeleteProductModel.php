<?php

namespace ProductsManagement;

use App\Database;

// Class responsible for handling the deletion of products in the admin panel
class DeleteProductModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to delete a product from the database
    public function deleteProduct()
    {
        // Retrieve the input data from the HTTP request and decode the JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the product ID from the input data
        $productId = isset($data['productId']) ? strip_tags($data['productId']) : null;

        // If the product ID is missing, return an error response
        if (!$productId) 
        {
            return ["success" => false, "message" => "Product ID missing"];
        }

        try 
        {
            // Query to retrieve the product's image path from the database
            $request = "SELECT path FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $product = $pdo->fetch();

            // If the product is not found, return an error response
            if (!$product) 
            {
                return ["success" => false, "message" => "Product not found"];
            }

            // Construct the full path of the product image
            $imagePath = 'assets/img/' . $product['path'];

            // Query to delete the product from the database
            $request = "DELETE FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);

            // Check if the product was successfully deleted
            if ($pdo->rowCount() > 0) 
            {
                // If the image file exists, delete it from the file system
                if (file_exists($imagePath)) 
                {
                    unlink($imagePath);
                }

                // Return a success response if the product was deleted
                return ["success" => true, "message" => "Product deleted successfully!!!"];
            } 
            else 
            {
                // Return an error if the product was not found or deleted
                return ["success" => false, "message" => "Product not found"];
            }
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response if a database error occurs
            return ["success" => false, "message" => "Database error"];
        }
    }
}

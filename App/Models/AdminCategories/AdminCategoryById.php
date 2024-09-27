<?php

namespace AdminCategories;

use App\Database;

// Class to retrieve a specific category by its ID in the admin panel
class AdminCategoryById
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection();
    }

    // Method to retrieve a category by its ID
    public function getCategoryById()
    {
        // Retrieve the category ID from the GET request
        $categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : null;

        // Check if the category ID is missing
        if (empty($categoryId)) 
        {
            return ["success" => false, "message" => "Category ID missing"];
        }

        try 
        {
            // SQL query to select a category by its ID 
            $request = "SELECT * FROM categorie WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryId]); 
            $categoryById = $pdo->fetch(\PDO::FETCH_ASSOC); 

            // Check if the category was found
            if ($categoryById) 
            {
                // Return the category data with success
                return ["success" => true, "categoryById" => $categoryById]; 
            } 
            else 
            {
                // Return an error if no category was found
                return ["success" => false, "message" => "Category not found"]; 
            }
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

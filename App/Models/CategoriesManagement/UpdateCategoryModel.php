<?php

namespace CategoriesManagement;

use App\Database;

// Class to handle updating an existing category in the admin panel
class UpdateCategoryModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to update the category
    public function updateCategory()
    {
        // Retrieve the input data from the HTTP request and decode it from JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the category details from the input data
        $categoryId = isset($data['id']) ? strip_tags($data['id']) : null;
        $categoryName = isset($data['name']) ? trim(strip_tags($data['name'])) : null;
        $categoryDescription = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $categoryPageTitle = isset($data['page_title']) ? trim(strip_tags($data['page_title'])) : null;
        $categoryPageDescription = isset($data['page_description']) ? trim(strip_tags($data['page_description'])) : null;

        // Check if any required fields are missing
        if (empty($categoryName) || empty($categoryDescription) || empty($categoryPageTitle) || empty($categoryPageDescription)) 
        {
            return ["success" => false, "message" => "All fields must be filled"];
        }

        // Fetch the current category data from the database
        $request = "SELECT * FROM categorie WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$categoryId]);
        $existingCategory = $pdo->fetch(\PDO::FETCH_ASSOC);

        if (!$existingCategory) {
            return ["success" => false, "message" => "Category not found"];
        }

        // Check if no changes were made to the category details
        if (
            $categoryName == $existingCategory['name'] &&
            $categoryDescription  == $existingCategory['description'] &&
            $categoryPageTitle == $existingCategory['page_title'] &&
            $categoryPageDescription == $existingCategory['page_description']
        ) {
            return ["success" => false, "message" => "No changes detected"];
        }

        // Check if the new category name already exists
        if ($this->nameExist($categoryName, $categoryId)) 
        {
            return ["success" => false, "message" => "This name is already used"];
        }

        try 
        {
            // SQL query to update the category with the new values
            $request = "UPDATE categorie SET name = ?, description = ?, page_title = ?, page_description = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryName, $categoryDescription, $categoryPageTitle, $categoryPageDescription, $categoryId]);

            // Return a success response with the updated category data
            return ["success" => true, "message" => "Category updated successfully", "categoryUpdate" => 
            [
                'id' => $categoryId,
                'name' => $categoryName,
                'description' => $categoryDescription,
                'page_title' => $categoryPageTitle,
                'page_description' => $categoryPageDescription,
            ]];
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }

    // Private method to check if the category name already exists (excluding the current category ID)
    private function nameExist($categoryName, $categoryId = null)
    {
        // This query checks if the name exists, excluding the current category ID to avoid conflict when updating
        $query = "SELECT COUNT(*) FROM categorie WHERE name = ? AND id != ?";
        $pdo = $this->db->prepare($query);
        $pdo->execute([$categoryName, $categoryId]);
        return $pdo->fetchColumn() > 0;
    }
}

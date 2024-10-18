<?php

namespace Models\CategoriesManagement;

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

    // Method to check if the category exists by its ID
    public function getCategoryById($categoryId)
    {
        $request = "SELECT * FROM categorie WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$categoryId]);
        return $pdo->fetch(\PDO::FETCH_ASSOC); // Fetch the category from the database
    }

    // Method to update the category in the database
    public function updateCategory($categoryId, $categoryName, $categoryDescription, $categoryPageTitle, $categoryPageDescription)
    {
        try 
        {
            // SQL query to update the category with the new values
            $request = "UPDATE categorie SET name = ?, description = ?, page_title = ?, page_description = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryName, $categoryDescription, $categoryPageTitle, $categoryPageDescription, $categoryId]);

            // Return true if the update is successful
            return $pdo->rowCount() > 0;

        } 
        catch (\PDOException $e) 
        {
            // Throw an exception to be handled by the controller
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    // Method to check if the category name already exists (excluding the current category ID)
    public function nameExist($categoryName, $categoryId)
    {
        $query = "SELECT COUNT(*) FROM categorie WHERE name = ? AND id != ?";
        $pdo = $this->db->prepare($query);
        $pdo->execute([$categoryName, $categoryId]);
        return $pdo->fetchColumn() > 0; // Return true if the category name already exists
    }
}
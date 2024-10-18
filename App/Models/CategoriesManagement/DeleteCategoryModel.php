<?php
namespace Models\CategoriesManagement;
use App\Database;

// Class to handle deleting a category in the admin panel
class DeleteCategoryModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection();
    }

    // Method to delete a category by its ID from the database
    public function removeCategoryById($categoryId)
    {
        try 
        {
            // SQL query to delete the category by its ID 
            $request = "DELETE FROM categorie WHERE id = ?";
            $pdo = $this->db->prepare($request); 
            $pdo->execute([$categoryId]); 

            // Check if any rows were affected
            return $pdo->rowCount() > 0;

        } 
        catch (\PDOException $e) 
        {
            // Throw the database error to be handled by the controller
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
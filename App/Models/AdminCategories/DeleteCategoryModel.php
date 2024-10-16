<?php
namespace AdminCategories;
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

    // Method to delete a category by its ID
    public function deleteCategory()
    {
        // Retrieve the input data from the HTTP request and decode it from JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the category ID from the input data
        $categoryId = isset($data['categoryId']) ? strip_tags($data['categoryId']) : null;

        // Check if the category ID is missing
        if (empty($categoryId)) 
        {
            return ["success" => false, "message" => "Category ID missing"];
        }

        try 
        {
            // SQL query to delete the category by its ID 
            $request = "DELETE FROM categorie WHERE id = ?";
            $pdo = $this->db->prepare($request); 
            $pdo->execute([$categoryId]); 

            // Check if any rows were affected
            if ($pdo->rowCount() > 0) 
            {
                // Success response if the category was deleted
                return ["success" => true, "message" => "Category deleted successfully"];
            } 
            else 
            {
                // Error response if no category was found with that ID
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

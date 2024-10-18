<?php

namespace Models\CategoriesManagement;

use App\Database;

// Class to handle the retrieval of categories in the admin panel
class CategoryModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to retrieve all categories from the database
    public function getCategories()
    {
        try 
        {
            // SQL query to select all categories
            $request = "SELECT * FROM categorie";
            $pdo = $this->db->query($request);
            return $pdo->fetchAll(\PDO::FETCH_ASSOC); 

        } 
        catch (\PDOException $e) 
        {
            // Throw an exception to handle in the controller
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
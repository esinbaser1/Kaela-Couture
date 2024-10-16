<?php

namespace AdminCategories;

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
    public function getCategorie()
    {
        try 
        {
            // SQL query to select all categories
            $request = "SELECT * FROM categorie";
            $pdo = $this->db->query($request);
            $category = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            // Return the list of categories with a success response
            return ["success" => true, "category" => $category];
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

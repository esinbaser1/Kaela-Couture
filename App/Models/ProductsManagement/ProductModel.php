<?php

namespace Models\ProductsManagement;

use App\Database;

// Class to retrieve product data in the admin panel
class ProductModel
{
    protected $db; 

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection();
    }

    // Method to retrieve all products from the database along with their categories and sections
    public function getProduct()
    {
        try 
        {
            // SQL query to select all products and join them with their categories and sections
            $request = "SELECT product.*, categorie.name AS categorie, section.name AS section 
                        FROM product 
                        JOIN categorie ON product.categorie_id = categorie.id 
                        JOIN section ON product.section_id = section.id";
            
            $pdo = $this->db->query($request);
            return $pdo->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\PDOException $e)
        {
            // Throw exception if a database error occurs
            throw new \Exception("Database error");
        }
    }
}
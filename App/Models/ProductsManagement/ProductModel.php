<?php

namespace ProductsManagement;

use App\Database;

// Class to handle retrieving product in the admin panel
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
            $product = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            
            // Return success response along with the fetched product data
            return [ "success" => true, "product" => $product];
        }
        catch(\PDOException $e)
        {
            // Return failure response if a database error occurs
            return ["success" => false, "message" => "Database error"];
        }
    }
}

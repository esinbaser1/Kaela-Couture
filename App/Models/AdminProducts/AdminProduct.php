<?php

namespace AdminProducts;

use App\Database;

class AdminProduct
{
    protected $db;


    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getProduct()
    {
        try 
        {
            $request = "SELECT product.*, categorie.name AS categorie, section.name AS section FROM product JOIN categorie ON product.categorie_id = categorie.id JOIN section ON product.section_id = section.id" ;
            $pdo = $this->db->query($request);
            $product = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            
            return [ "success" => true, "product" => $product];
        }
        catch(\PDOException $e)
        {
            error_log("Error when retrieving categories: " . $e->getMessage());

            return ["success" => false, "message" => "Database error"];
        }
    }
}
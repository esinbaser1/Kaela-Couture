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

    public function getProductAndCategorie()
    {
        try 
        {
            $request = "SELECT image.*, categorie.name AS categorie FROM image JOIN categorie ON image.categorie_id = categorie.id";
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
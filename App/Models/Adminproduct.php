<?php

namespace Models;

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
            $products = $pdo->fetchAll();
            
            return [ "success" => true, "products" => $products];
        }
        catch(\PDOException $e)
        {
            error_log("Error when retrieving categories: " . $e->getMessage());

            return ["success" => false, "message" => "Database error"];
        }
    }
}
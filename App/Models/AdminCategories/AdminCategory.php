<?php

namespace AdminCategories;

use App\Database;

class AdminCategory 
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getCategorie()
    {
        try 
        {
            $request = "SELECT * FROM categorie";
            $pdo = $this->db->query($request);
            $category = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            return ["success" => true, "category" => $category];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when retrieving categories: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

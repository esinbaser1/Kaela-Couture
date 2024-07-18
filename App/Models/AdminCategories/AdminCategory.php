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
        try {
            $request = "SELECT * FROM categorie";
            $pdo = $this->db->query($request);
            $categorie = $pdo->fetchAll();

            return ["success" => true, "categorie" => $categorie];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when retrieving categories: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

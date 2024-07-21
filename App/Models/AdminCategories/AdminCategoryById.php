<?php

namespace AdminCategories;

use App\Database;

class AdminCategoryById
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getCategoryById()
    {
        $categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : null;

        if (empty($categoryId)) 
        {
            return ["success" => false, "message" => "Category ID missing"];
        }

        try 
        {
            $request = "SELECT * FROM categorie WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryId]);
            $categoryById = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($categoryById) 
            {
                return ["success" => true, "categoryById" => $categoryById];
            } 
            else 
            {
                return ["success" => false, "message" => "Category not found"];
            }
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when fetching category: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

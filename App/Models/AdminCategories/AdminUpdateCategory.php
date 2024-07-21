<?php

namespace AdminCategories;

use App\Database;

class AdminUpdateCategory
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function updateCategory()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $categoryId = isset($data['id']) ? strip_tags($data['id']) : null;
        $categoryName = isset($data['name']) ? strip_tags($data['name']) : null;
        $categoryDescription = isset($data['description']) ? strip_tags($data['description']) : null;
        $categoryPageTitle = isset($data['page_title']) ? strip_tags($data['page_title']) : null;
        $categoryPageDescription = isset($data['page_description']) ? strip_tags($data['page_description']) : null;

        try 
        {
            // Récupérer les valeurs actuelles de la catégorie
            $currentCategory = $this->getCategoryById($categoryId);

            if (!$currentCategory) {
                return ["success" => false, "message" => "Category not found"];
            }

            // Utiliser les valeurs actuelles si les nouvelles sont nulles
            $categoryName = $categoryName ?? $currentCategory['name'];
            $categoryDescription = $categoryDescription ?? $currentCategory['description'];
            $categoryPageTitle = $categoryPageTitle ?? $currentCategory['page_title'];
            $categoryPageDescription = $categoryPageDescription ?? $currentCategory['page_description'];

            $request = "UPDATE categorie SET name = ?, description = ?, page_title = ?, page_description = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryName, $categoryDescription, $categoryPageTitle, $categoryPageDescription, $categoryId]);

            return ["success" => true, "message" => "Category updated successfully", "categoryUpdate" => [
                'id' => $categoryId,
                'name' => $categoryName,
                'description' => $categoryDescription,
                'page_title' => $categoryPageTitle,
                'page_description' => $categoryPageDescription,
            ]];
        } catch (\PDOException $e) 
        {
            error_log("Error when updating category : " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    private function getCategoryById($categoryId)
    {
        try 
        {
            $request = "SELECT * FROM categorie WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryId]);
            return $pdo->fetch(\PDO::FETCH_ASSOC);
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when fetching category: " . $e->getMessage());
            return false;
        }
    }
}

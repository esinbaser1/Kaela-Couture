<?php

namespace AdminCategories;

use Lib\Slug;
use App\Database;

class AdminAddCategory
{
    protected $db;
    protected $slug;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    public function addCategory()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $categoryName = isset($data['categoryName']) ? trim(strip_tags($data['categoryName'])) : null;
        $description = isset($data['categoryDescription']) ? trim(strip_tags($data['categoryDescription'])) : null;
        $pageTitle = isset($data['categoryPageTitle']) ? trim(strip_tags($data['categoryPageTitle'])) : null;
        $pageDescription = isset($data['categoryPageDescription']) ? trim(strip_tags($data['categoryPageDescription'])) : null;

        if (empty($categoryName) || empty($description) || empty($pageTitle) || empty($pageDescription)) 
        {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        $categoryNameSlug = $this->slug->sluguer($categoryName);

        if ($this->nameExist($categoryName)) {
            return ["success" => false, "message" => "This name is already used"];
        }

        try 
        {
            $request = "INSERT INTO categorie (name, description, page_title, page_description, slug) VALUES (?, ?, ?, ?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryName, $description, $pageTitle, $pageDescription, $categoryNameSlug]);

            $categoryId = $this->db->lastInsertId();
            
            $newCategory = [
                'id' => $categoryId,
                'name' => $categoryName,
                'description' => $description,
                'page_title' => $pageTitle,
                'page_description' => $pageDescription,
                'slug' => $categoryNameSlug,
            ];

            return ["success" => true, "message" => "Category added successfully!!!", "category" => $newCategory];

        } 
        catch (\PDOException $e) 
        {
            error_log("Error when creating category: " . $e->getMessage());
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }
    private function nameExist($categoryName)
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM categorie WHERE name = ?");
        $pdo->execute([$categoryName]);
        return $pdo->fetchColumn() > 0;
    }
}

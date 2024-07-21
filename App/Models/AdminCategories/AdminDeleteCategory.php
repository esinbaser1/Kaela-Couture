<?php
namespace AdminCategories;
use App\Database;

class AdminDeleteCategory
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function deleteCategory()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $categoryId = isset($data['categoryId']) ? strip_tags($data['categoryId']) : null;

        if (empty($categoryId)) 
        {
            return ["success" => false, "message" => "Category ID missing"];
        }

        try 
        {
            $request = "DELETE FROM categorie WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryId]);

            if ($pdo->rowCount() > 0) 
            {
                return ["success" => true, "message" => "Category deleted successfully"];
            } 
            else 
            {
                return ["success" => false, "message" => "Category not found"];
            }
        }
         catch (\PDOException $e) 
        {
            error_log("Error when deleting category: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}


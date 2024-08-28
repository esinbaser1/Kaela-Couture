<?php

namespace AdminCategories;

use App\Database;

class AdminUpdateCategory
{
    protected $db;
    protected $adminCategoryById;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->adminCategoryById = new AdminCategoryById();
    }

    public function updateCategory()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $categoryId = isset($data['id']) ? strip_tags($data['id']) : null;
        $categoryName = isset($data['name']) ? trim(strip_tags($data['name'])) : null;
        $categoryDescription = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $categoryPageTitle = isset($data['page_title']) ? trim(strip_tags($data['page_title'])) : null;
        $categoryPageDescription = isset($data['page_description']) ? trim(strip_tags($data['page_description'])) : null;


        if (empty($categoryName) || empty($categoryDescription) || empty($categoryPageTitle) || empty($categoryPageDescription)) {
            return ["success" => false, "message" => "All fields must be filled"];
        }

        // Récupérer les données actuelles du produit
        $request = "SELECT * FROM categorie WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$categoryId]);
        $existingCategory = $pdo->fetch((\PDO::FETCH_ASSOC));

        // Vérifier si aucune modification n'a été apportée

        if (
            $categoryName == $existingCategory['name'] &&
            $categoryDescription  == $existingCategory['description'] &&
            $categoryPageTitle == $existingCategory['page_title'] &&
            $categoryPageDescription == $existingCategory['page_description']
        ) {
            return ["success" => false, "message" => "No changes detected"];
        }

        if ($this->nameExist($categoryName, $categoryId)) {
            return ["success" => false, "message" => "This name is already used"];
        }
        

        try {
            // Sauvegarder l'ID de la catégorie dans $_GET pour réutiliser la méthode existante
            $_GET['categoryId'] = $categoryId;

            // Récupérer les valeurs actuelles de la catégorie en utilisant la méthode existante
            $currentCategory = $this->adminCategoryById->getCategoryById();

            if (!$currentCategory['success']) {
                return ["success" => false, "message" => "Category not found"];
            }

            $currentCategoryData = $currentCategory['categoryById'];

            // Utiliser les valeurs actuelles si les nouvelles sont nulles
            $categoryName = $categoryName ?? $currentCategoryData['name'];
            $categoryDescription = $categoryDescription ?? $currentCategoryData['description'];
            $categoryPageTitle = $categoryPageTitle ?? $currentCategoryData['page_title'];
            $categoryPageDescription = $categoryPageDescription ?? $currentCategoryData['page_description'];

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
        } catch (\PDOException $e) {
            error_log("Error when updating category : " . $e->getMessage());

            return ["success" => false, "message" => "Database error"];
        }
    }

    private function nameExist($categoryName, $categoryId = null)
{
  //En ajoutant AND id != ?, la vérification d'unicité ne prend pas en compte le nom actuelle, ca empêche le système de détecter le nom de la catégorie comme déjà utilisé s'il n'a pas changé car si non ca me met toujours This name is already used meme si je n'ai pas modifié le nom
    $query = "SELECT COUNT(*) FROM categorie WHERE name = ? AND id != ?";
    $pdo = $this->db->prepare($query);
    $pdo->execute([$categoryName, $categoryId]);
    return $pdo->fetchColumn() > 0;
}
}

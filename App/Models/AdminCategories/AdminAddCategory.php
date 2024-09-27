<?php

namespace AdminCategories;

use Lib\Slug;
use App\Database;

// Class to handle adding new categories in the admin panel
class AdminAddCategory
{
    protected $db;  
    protected $slug;

    // Initializes the database connection and slug generator
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    // Method to add a new category
    public function addCategory()
    {
        // Get the input data from the HTTP request and decode the JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and validate the input fields
        $categoryName = isset($data['categoryName']) ? trim(strip_tags($data['categoryName'])) : null;
        $description = isset($data['categoryDescription']) ? trim(strip_tags($data['categoryDescription'])) : null;
        $pageTitle = isset($data['categoryPageTitle']) ? trim(strip_tags($data['categoryPageTitle'])) : null;
        $pageDescription = isset($data['categoryPageDescription']) ? trim(strip_tags($data['categoryPageDescription'])) : null;

        // Check if any required fields are missing
        if (empty($categoryName) || empty($description) || empty($pageTitle) || empty($pageDescription)) 
        {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        // Generate a slug for the category name
        $categoryNameSlug = $this->slug->sluguer($categoryName);

        // Check if the category name already exists in the database
        if ($this->nameExist($categoryName)) 
        {
            return ["success" => false, "message" => "This name is already used"];
        }

        try 
        {
            // SQL query to insert the new category into the database
            $request = "INSERT INTO categorie (name, description, page_title, page_description, slug) VALUES (?, ?, ?, ?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryName, $description, $pageTitle, $pageDescription, $categoryNameSlug]);

            // Get the ID of the newly inserted category
            $categoryId = $this->db->lastInsertId();
            
            // Prepare the new category data to return in the response
            $newCategory = [
                'id' => $categoryId,
                'name' => $categoryName,
                'description' => $description,
                'page_title' => $pageTitle,
                'page_description' => $pageDescription,
                'slug' => $categoryNameSlug,
            ];

            // Return a success response with the new category data
            return ["success" => true, "message" => "Category added successfully!!!", "category" => $newCategory];

        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    // Private method to check if a category name already exists in the database
    private function nameExist($categoryName)
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM categorie WHERE name = ?");
        $pdo->execute([$categoryName]);
        return $pdo->fetchColumn() > 0;
    }
}

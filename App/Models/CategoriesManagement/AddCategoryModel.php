<?php

namespace Models\CategoriesManagement;

use Lib\Slug;
use App\Database;

// Class to handle adding new categories in the admin panel
class AddCategoryModel
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

    // Method to add a new category to the database
    public function addCategory($categoryName, $description, $pageTitle, $pageDescription, $categoryNameSlug)
    {
        try 
        {
            // SQL query to insert the new category into the database
            $request = "INSERT INTO categorie (name, description, page_title, page_description, slug) VALUES (?, ?, ?, ?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$categoryName, $description, $pageTitle, $pageDescription, $categoryNameSlug]);

            // Get the ID of the newly inserted category
            $categoryId = $this->db->lastInsertId();

            // Return the new category ID for further use
            return $categoryId;

        } 
        catch (\PDOException $e) 
        {
            // Handle and propagate database errors
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    // Check if a category name already exists in the database
    public function nameExist($categoryName)
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM categorie WHERE name = ?");
        $pdo->execute([$categoryName]);
        return $pdo->fetchColumn() > 0;
    }
}
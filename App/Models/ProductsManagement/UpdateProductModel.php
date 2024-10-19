<?php

namespace Models\ProductsManagement;

use App\Database;

// Class responsible for updating existing products in the admin panel
class UpdateProductModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to retrieve a product by its ID
    public function getProduct($productId)
    {
        try 
        {
            $request = "SELECT * FROM product WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            return $pdo->fetch(\PDO::FETCH_ASSOC);
        } 
        catch (\PDOException $e) 
        {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    // Method to update a product in the database
    public function updateProduct($productId, $productName, $productDescription, $imagePath, $productSlug, $productCategory, $productSection)
    {
        try 
        {
            $request = "UPDATE product SET name = ?, description = ?, path = ?, slug = ?, categorie_id = ?, section_id = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productName, $productDescription, $imagePath, $productSlug, $productCategory, $productSection, $productId]);
        } 
        catch (\PDOException $e) 
        {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    // Method to check if a product name already exists for another product
    public function nameExist($productName, $productId)
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM product WHERE name = ? AND id != ?");
        $pdo->execute([$productName, $productId]);
        return $pdo->fetchColumn() > 0;
    }
}
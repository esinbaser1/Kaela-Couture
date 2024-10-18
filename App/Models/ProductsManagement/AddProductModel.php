<?php

namespace Models\ProductsManagement;

use App\Database;
use Lib\Slug;
use Utils\ConvertToWebP;

class AddProductModel
{
    protected $db;
    protected $slug;

    // Initializes the database connection and Slug utility
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    // Method to add a new product to the database
    public function addProduct($productName, $productDescription, $productCategory, $productSection, $webpImagePath, $productSlug)
    {
        try 
        {
            // Insert the new product into the database
            $request = "INSERT INTO product (name, description, path, slug, categorie_id, section_id) VALUES (?,?,?,?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productName, $productDescription, $webpImagePath, $productSlug, $productCategory, $productSection]);

            // Get the ID of the newly inserted product
            $productId = $this->db->lastInsertId();

            return $productId;

        } catch (\PDOException $e) {
            throw new \Exception("Database error");
        }
    }

    // Method to update product path with the new WebP file name
    public function updateProductImagePath($newWebpFileName, $productId)
    {
        $request = "UPDATE product SET path = ? WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$newWebpFileName, $productId]);
    }

    // Private method to check if the product name already exists
    public function nameExist($productName) 
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM product WHERE name = ?");
        $pdo->execute([$productName]);
        return $pdo->fetchColumn() > 0;
    }
}
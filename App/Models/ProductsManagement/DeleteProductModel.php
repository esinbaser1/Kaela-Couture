<?php

namespace Models\ProductsManagement;

use App\Database;

class DeleteProductModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to get the product image path
    public function getProductImagePath($productId)
    {
        $request = "SELECT path FROM product WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$productId]);
        return $pdo->fetch();
    }

    // Method to delete a product from the database
    public function deleteProduct($productId)
    {
        $request = "DELETE FROM product WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$productId]);

        return $pdo->rowCount() > 0;
    }
}
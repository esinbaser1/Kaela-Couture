<?php

namespace AdminProducts;

use App\Database;

class AdminDeleteProduct
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function deleteProduct()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $productId = $data['productId'] ?? null;

        if (!$productId) 
        {
            return ["success" => false, "message" => "Product ID missing"];
        }

        try 
        {
            // Récupérer le chemin de l'image
            $request = "SELECT path FROM image WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $product = $pdo->fetch();

            if (!$product) 
            {
                return ["success" => false, "message" => "Product not found"];
            }

            $imagePath = 'assets/img/' . $product['path'];

            // Supprimer l'entrée de la base de données
            $request = "DELETE FROM image WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);

            if ($pdo->rowCount() > 0) 
            {
                // Supprimer le fichier image du système de fichiers
                if (file_exists($imagePath)) 
                {
                    unlink($imagePath);
                }

                return ["success" => true, "message" => "Product deleted successfully"];
            } 
            else 
            {
                return ["success" => false, "message" => "Product not found"];
            }
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when deleting product: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

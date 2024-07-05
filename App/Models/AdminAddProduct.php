<?php

namespace Models;

use App\Database;
use Lib\Slug;

class AdminAddProduct 
{
    protected $db;
    protected $slug;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->slug = new Slug();
    }

    public function addProduct()
    {
        $productName = isset($_POST['productName']) ? strip_tags($_POST['productName']) : null;
        $productDescription = isset($_POST['productDescription']) ? strip_tags($_POST['productDescription']) : null;
        $productCategory = isset($_POST['productCategory']) ? $_POST['productCategory'] : null;
        $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;
        $productSlug = $this->slug->sluguer($productName);

        if (empty($productName) || empty($productDescription) || empty($productCategory) || empty($productImage)) 
        {
            return ["success" => false, "message" => "All fields are required"];
        }

        $imageLocation = "assets/img/";
        $imagePath = $imageLocation . basename($productImage['name']);

        if (!move_uploaded_file($productImage['tmp_name'], $imagePath)) 
        {
            return ["success" => false, "message" => "Failed to move uploaded file"];
        }

        try {
            $request = "INSERT INTO image (name, description, path, slug, categorie_id) VALUES (?,?,?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productName, $productDescription, $imagePath, $productSlug, $productCategory]);

            return ["success" => true, "message" => "Product added successfully"];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when creating product: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    public function getCategorie()
    {
        try {
            $request = "SELECT id, name FROM categorie";
            $pdo = $this->db->query($request);
            $categories = $pdo->fetchAll();

            return ["success" => true, "categories" => $categories];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when retrieving categories: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

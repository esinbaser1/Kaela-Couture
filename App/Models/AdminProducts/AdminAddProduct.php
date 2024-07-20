<?php

namespace AdminProducts;

use App\Database;
use Lib\Slug;
use Components\ConvertToWebP;

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
        $productCategory = isset($_POST['productCategory']) ? strip_tags($_POST['productCategory']) : null;
        $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;

        if (empty($productName) || empty($productDescription) || empty($productCategory) || empty($productImage)) 
        {
            return ["success" => false, "message" => "All fields are required"];
        }

        $productSlug = $this->slug->sluguer($productName);
        $imageLocation = "assets/img/";

        $tempImagePath = $imageLocation . basename($productImage['name']);

        if (!move_uploaded_file($productImage['tmp_name'], $tempImagePath)) 
        {
            return ["success" => false, "message" => "Failed to move uploaded file"];
        }

        $converter = new ConvertToWebP();
        $webpImagePath = $converter->convertToWebP($tempImagePath, $imageLocation, $productSlug, $productCategory);

        if (!$webpImagePath) 
        {
            return ["success" => false, "message" => "Failed to convert image to WebP format"];
        }

        try 
        {
            $request = "INSERT INTO image (name, description, path, slug, categorie_id) VALUES (?,?,?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productName, $productDescription, $webpImagePath, $productSlug, $productCategory]);

            $productId = $this->db->lastInsertId();
            $newWebpFileName =  $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
            $newWebpImagePath = $imageLocation . $newWebpFileName;

            if (!rename($webpImagePath, $newWebpImagePath)) 
            {
                return ["success" => false, "message" => "Failed to rename WebP image"];
            }

            $request = "UPDATE image SET path = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$newWebpFileName, $productId]);

            $newProduct = [
                'id' => $productId,
                'name' => $productName,
                'description' => $productDescription,
                'path' => $newWebpFileName,
                'slug' => $productSlug,
                'category_id' => $productCategory
            ];

            return ["success" => true, "message" => "Product added successfully", "product" => $newProduct];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when creating product: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

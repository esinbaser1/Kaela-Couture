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
        $productName = isset($_POST['productName']) ? trim(strip_tags($_POST['productName'])) : null;
        $productDescription = isset($_POST['productDescription']) ? trim(strip_tags($_POST['productDescription'])) : null;
        $productCategory = isset($_POST['productCategory']) ? strip_tags($_POST['productCategory']) : null;
        $productSection = isset($_POST['productSection']) ? strip_tags($_POST['productSection']) : null;
        $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;

        if (empty($productName) || empty($productDescription) || empty($productCategory) || empty($productImage) || empty($productSection)) 
        {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        if ($this->nameExist($productName)) {
           return ["success" => false, "message" => "This name is already used"];
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
            $request = "INSERT INTO product (name, description, path, slug, categorie_id, section_id) VALUES (?,?,?,?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productName, $productDescription, $webpImagePath, $productSlug, $productCategory, $productSection]);

            $productId = $this->db->lastInsertId();
            $newWebpFileName =  $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
            $newWebpImagePath = $imageLocation . $newWebpFileName;

            if (!rename($webpImagePath, $newWebpImagePath)) 
            {
                return ["success" => false, "message" => "Failed to rename WebP image"];
            }

            $request = "UPDATE product SET path = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$newWebpFileName, $productId]);

            $newProduct = [
                'id' => $productId,
                'name' => $productName,
                'description' => $productDescription,
                'path' => $newWebpFileName,
                'slug' => $productSlug,
                'category_id' => $productCategory,
                'section_id' => $productSection
            ];

            return ["success" => true, "message" => "Product added successfully", "product" => $newProduct];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when creating product: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    private function nameExist($productName) {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM product WHERE name = ?");
        $pdo->execute([$productName]);
        return $pdo->fetchColumn() > 0;
    }
}
<?php

namespace Controllers\ProductsManagement;

use Models\ProductsManagement\UpdateProductModel;
use Lib\Slug;
use Utils\ConvertToWebP;

class UpdateProductController
{
    protected $model;
    protected $slug;

    public function __construct()
    {
        $this->model = new UpdateProductModel();
        $this->slug = new Slug();
    }

    // Method to retrieve a product by ID
    public function getProductById()
    {
        $productId = isset($_GET['productId']) ? strip_tags($_GET['productId']) : null;

        if (!$productId) 
        {
            return ["success" => false, "message" => "Product ID is missing"];
        }

        try 
        {
            $product = $this->model->getProduct($productId);
            return ["success" => true, "product" => $product];
        } 
        catch (\Exception $e) 
        {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Method to update a product
    public function updateProduct()
    {
        // Sanitize and retrieve the informations details from the request
        $productId = isset($_POST['productId']) ? strip_tags($_POST['productId']) : null;
        $productName = isset($_POST['productName']) ? trim(strip_tags($_POST['productName'])) : null;
        $productDescription = isset($_POST['productDescription']) ? trim(strip_tags($_POST['productDescription'])) : null;
        $productCategory = isset($_POST['productCategory']) ? strip_tags($_POST['productCategory']) : null;
        $productSection = isset($_POST['productSection']) ? strip_tags($_POST['productSection']) : null;
        $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;

        // Checks if id is present
        if (!$productId) 
        {
            return ["success" => false, "message" => "Product ID is required"];
        }

        // Fetch the existing product data
        try 
        {
            $existingProduct = $this->model->getProduct($productId);
            if (!$existingProduct) 
            {
                return ["success" => false, "message" => "Product not found"];
            }
        } 
        catch (\Exception $e) 
        {
            return ["success" => false, "message" => $e->getMessage()];
        }

        // Handle slug generation and other fields
        $productSlug = $this->slug->sluguer($productName ?: $existingProduct['name']);

        // Handle image upload if a new image is provided
        if ($productImage) 
        {
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

            // Rename WebP image
            $newWebpFileName = $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
            $newWebpImagePath = $imageLocation . $newWebpFileName;

            if (!rename($webpImagePath, $newWebpImagePath)) 
            {
                return ["success" => false, "message" => "Failed to rename WebP image"];
            }

            // Delete the old image if it exists
            if (file_exists("assets/img/" . $existingProduct['path'])) 
            {
                unlink("assets/img/" . $existingProduct['path']);
            }

            $imagePath = $newWebpFileName;
        } 
        else 
        {
            $imagePath = $existingProduct['path'];
        }

        // Call the model to update the product in the database
        try 
        {
            $this->model->updateProduct(
                $productId,
                $productName ? : $existingProduct['name'],
                $productDescription ? : $existingProduct['description'],
                $imagePath,
                $productSlug,
                $productCategory ? : $existingProduct['categorie_id'],
                $productSection ? : $existingProduct['section_id']
            );
            return ["success" => true, "message" => "Product updated successfully"];
        } 
        catch (\Exception $e) 
        {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}

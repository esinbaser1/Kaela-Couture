<?php

namespace Controllers\ProductsManagement;

use Models\ProductsManagement\AddProductModel;
use Utils\ConvertToWebP;
use Lib\Slug;

class AddProductController
{
    protected $model;
    protected $slug;

    public function __construct()
    {
        $this->model = new AddProductModel();
        $this->slug = new Slug();
    }

    // Method to handle adding new product
    public function addProduct()
    {
        // Retrieve and sanitize input fields from the POST request
        $productName = isset($_POST['productName']) ? trim(strip_tags($_POST['productName'])) : null;
        $productDescription = isset($_POST['productDescription']) ? trim(strip_tags($_POST['productDescription'])) : null;
        $productCategory = isset($_POST['productCategory']) ? strip_tags($_POST['productCategory']) : null;
        $productSection = isset($_POST['productSection']) ? strip_tags($_POST['productSection']) : null;
        $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;

        // Check if any required fields are missing
        if (empty($productName) || empty($productDescription) || empty($productCategory) || empty($productImage) || empty($productSection)) {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        // Check if the product name already exists in the database
        if ($this->model->nameExist($productName)) {
            return ["success" => false, "message" => "This name is already used"];
        }

        // Create a slug for the product name
        $productSlug = $this->slug->sluguer($productName);
        $imageLocation = "assets/img/"; // Define the location for storing images

        // Temporary path for the uploaded image
        $tempImagePath = $imageLocation . basename($productImage['name']);

        // Move the uploaded file to the temporary path
        if (!move_uploaded_file($productImage['tmp_name'], $tempImagePath)) {
            return ["success" => false, "message" => "Failed to move uploaded file"];
        }

        // Convert the image to WebP format
        $converter = new ConvertToWebP();
        $webpImagePath = $converter->convertToWebP($tempImagePath, $imageLocation, $productSlug, $productCategory);

        // Check if the WebP conversion was successful
        if (!$webpImagePath) {
            return ["success" => false, "message" => "Failed to convert image to WebP format"];
        }

        // Add the product to the database via the model
        try {
            $productId = $this->model->addProduct($productName, $productDescription, $productCategory, $productSection, $webpImagePath, $productSlug);

            // Rename the WebP file to include the product ID and category
            $newWebpFileName = $productSlug . '-' . $productId . '-' . $productCategory . '.webp';
            $newWebpImagePath = $imageLocation . $newWebpFileName;

            // Rename the WebP file
            if (!rename($webpImagePath, $newWebpImagePath)) {
                return ["success" => false, "message" => "Failed to rename WebP image"];
            }

            // Update the product path in the database with the new WebP file name
            $this->model->updateProductImagePath($newWebpFileName, $productId);

            // Prepare the new product data for the response
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

        } catch (\Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
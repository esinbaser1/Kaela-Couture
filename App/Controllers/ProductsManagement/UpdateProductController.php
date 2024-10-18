<?php

namespace Controllers\ProductsManagement;

use Models\ProductsManagement\UpdateProductModel;

class UpdateProductController
{
    protected $model;

    public function __construct()
    {
        $this->model = new UpdateProductModel();
    }

    // Method to retrieve a product by ID
    public function getProductById()
    {
        $productId = isset($_GET['productId']) ? strip_tags($_GET['productId']) : null;

        if (!$productId) {
            return ["success" => false, "message" => "Product ID is missing"];
        }

        try {
            // Call the model to get the product by ID
            $product = $this->model->getProduct($productId);
            return ["success" => true, "product" => $product];
        } catch (\Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Existing method to update a product
    public function updateProduct()
    {
        $productId = isset($_POST['productId']) ? strip_tags($_POST['productId']) : null;
        $productName = isset($_POST['productName']) ? trim(strip_tags($_POST['productName'])) : null;
        $productDescription = isset($_POST['productDescription']) ? trim(strip_tags($_POST['productDescription'])) : null;
        $productCategory = isset($_POST['productCategory']) ? strip_tags($_POST['productCategory']) : null;
        $productSection = isset($_POST['productSection']) ? strip_tags($_POST['productSection']) : null;
        $productImage = isset($_FILES['productImage']) ? $_FILES['productImage'] : null;

        if (!$productId || !$productName || !$productDescription || !$productCategory || !$productSection) {
            return ["success" => false, "message" => "All fields must be filled"];
        }

        try {
            $updatedProduct = $this->model->updateProduct($productId, $productName, $productDescription, $productCategory, $productSection, $productImage);
            return ["success" => true, "message" => "Product updated successfully", "product" => $updatedProduct];
        } catch (\Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
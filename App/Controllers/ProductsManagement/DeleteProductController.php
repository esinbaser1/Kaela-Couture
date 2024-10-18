<?php

namespace Controllers\ProductsManagement;

use Models\ProductsManagement\DeleteProductModel;

class DeleteProductController
{
    protected $model;

    public function __construct()
    {
        $this->model = new DeleteProductModel();
    }

    // Method to handle the product deletion request
    public function deleteProduct()
    {
        // Retrieve the input data from the HTTP request and decode the JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the product ID from the input data
        $productId = isset($data['productId']) ? strip_tags($data['productId']) : null;

        // If the product ID is missing, return an error response
        if (!$productId) {
            return ["success" => false, "message" => "Product ID missing"];
        }

        // Query the model to get the product image path
        $product = $this->model->getProductImagePath($productId);

        // If the product is not found, return an error response
        if (!$product) {
            return ["success" => false, "message" => "Product not found"];
        }

        // Construct the full path of the product image
        $imagePath = 'assets/img/' . $product['path'];

        // Delete the product from the database using the model
        $isDeleted = $this->model->deleteProduct($productId);

        // Check if the product was successfully deleted
        if ($isDeleted) {
            // If the image file exists, delete it from the file system
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Return a success response if the product was deleted
            return ["success" => true, "message" => "Product deleted successfully!!!"];
        } else {
            // Return an error if the product was not found or deleted
            return ["success" => false, "message" => "Product not found"];
        }
    }
}
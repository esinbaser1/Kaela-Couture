<?php 

namespace Controllers\ProductsManagement;

use Models\ProductsManagement\ProductModel;

class ProductController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    // Method to retrieve all products
    public function getProduct()
    {
        try 
        {
            // Get product data from the model
            $product = $this->model->getProduct();
            return ["success" => true, "product" => $product];
        } 
        catch (\Exception $e) 
        {
            // Return error message if exception occurs
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
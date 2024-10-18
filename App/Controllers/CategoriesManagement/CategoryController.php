<?php

namespace Controllers\CategoriesManagement;

use Models\CategoriesManagement\CategoryModel;

class CategoryController 
{
    protected $model;

    // Initializes the model
    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    // Method to handle the logic for retrieving all categories
    public function getCategories()
    {
        try 
        {
            // Fetch the categories from the model
            $categories = $this->model->getCategories();

            // Return the list of categories with a success response
            return ["success" => true, "category" => $categories];
        } 
        catch (\Exception $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
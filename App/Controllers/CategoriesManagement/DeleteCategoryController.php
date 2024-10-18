<?php

namespace Controllers\CategoriesManagement;

use Models\CategoriesManagement\DeleteCategoryModel;

class DeleteCategoryController 
{
    protected $model;

    // Initializes the model
    public function __construct()
    {
        $this->model = new DeleteCategoryModel();
    }

    // Method to handle the logic for deleting a category
    public function deleteCategory()
    {
        // Retrieve the input data from the HTTP request and decode it from JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the category ID from the input data
        $categoryId = isset($data['categoryId']) ? strip_tags($data['categoryId']) : null;

        // Check if the category ID is missing
        if (empty($categoryId)) 
        {
            return ["success" => false, "message" => "Category ID missing"];
        }

        try 
        {
            // Delete the category using the model
            $isDeleted = $this->model->removeCategoryById($categoryId);

            // Check if the deletion was successful
            if ($isDeleted) 
            {
                return ["success" => true, "message" => "Category deleted successfully"];
            } 
            else 
            {
                return ["success" => false, "message" => "Category not found"];
            }

        } 
        catch (\Exception $e) 
        {
            // Return a failure response in case of errors
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
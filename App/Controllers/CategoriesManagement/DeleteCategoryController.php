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
         // Sanitize and get the social network ID from the HTTP request
        $categoryId = isset($_GET['categoryId']) ? strip_tags($_GET['categoryId']) : null;

        // Check if the ID is missing
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

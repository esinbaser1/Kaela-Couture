<?php

namespace Controllers\CategoriesManagement;

use Models\CategoriesManagement\UpdateCategoryModel;

class UpdateCategoryController 
{
    protected $model;

    // Initializes the model
    public function __construct()
    {
        $this->model = new UpdateCategoryModel();
    }

    // Method to handle fetching a category by its ID
    public function getCategoryById($categoryId = null)
    {
        // If the ID is not passed, retrieve it from the GET request
        $categoryId = $categoryId ?? (isset($_GET['categoryId']) ? $_GET['categoryId'] : null);
    
        // Check if the ID is present
        if (empty($categoryId)) 
        {
            return ["success" => false, "message" => "Category ID missing"];
        }
    
        // Fetch category data from the model
        $category = $this->model->getCategoryById($categoryId);
    
        if ($category) 
        {
            return ["success" => true, "category" => $category];
        } 
        else 
        {
            return ["success" => false, "message" => "Category not found"];
        }
    }
    
    // Method to handle the category update logic
    public function updateCategory()
    {
        // Retrieve data from the HTTP request
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
    
        // Sanitize and retrieve category details
        $categoryId = isset($data['id']) ? strip_tags($data['id']) : null;
        $categoryName = isset($data['name']) ? trim(strip_tags($data['name'])) : null;
        $categoryDescription = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $categoryPageTitle = isset($data['page_title']) ? trim(strip_tags($data['page_title'])) : null;
        $categoryPageDescription = isset($data['page_description']) ? trim(strip_tags($data['page_description'])) : null;
    
        // Check if any required fields are missing
        if (empty($categoryName) || empty($categoryDescription) || empty($categoryPageTitle) || empty($categoryPageDescription)) 
        {
            return ["success" => false, "message" => "All fields must be filled"];
        }
    
        try 
        {
            // Explicitly pass the category ID
            $existingCategory = $this->getCategoryById($categoryId);
    
            // Check if the category exists
            if (!$existingCategory['success']) 
            {
                return $existingCategory;  // Return the error message from getCategoryById
            }
    
            // Check if any changes were made
            if (
                $categoryName == $existingCategory['category']['name'] &&
                $categoryDescription == $existingCategory['category']['description'] &&
                $categoryPageTitle == $existingCategory['category']['page_title'] &&
                $categoryPageDescription == $existingCategory['category']['page_description']
            ) 
            {
                return ["success" => false, "message" => "No changes detected"];
            }
    
            // Check if the new category name already exists
            if ($this->model->nameExist($categoryName, $categoryId)) 
            {
                return ["success" => false, "message" => "This name is already used"];
            }
    
            // Update the category in the model
            $isUpdated = $this->model->updateCategory($categoryId, $categoryName, $categoryDescription, $categoryPageTitle, $categoryPageDescription);
    
            // Check if the update was successful
            if ($isUpdated) 
            {
                // Return the new data to the front end
                return ["success" => true, "message" => "Category updated successfully", "categoryUpdate" => 
                [
                    'id' => $categoryId,
                    'name' => $categoryName,
                    'description' => $categoryDescription,
                    'page_title' => $categoryPageTitle,
                    'page_description' => $categoryPageDescription,
                ]];
            } 
            else 
            {
                return ["success" => false, "message" => "No changes detected or update failed"];
            }
    
        } 
        catch (\Exception $e) 
        {
            // Return a failure response in case of an exception
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
<?php

namespace Controllers\CategoriesManagement;

use Models\CategoriesManagement\AddCategoryModel;
use Lib\Slug;

class AddCategoryController 
{
    protected $model;
    protected $slug;

    // Initializes the model and slug generator
    public function __construct()
    {
        $this->model = new AddCategoryModel();
        $this->slug = new Slug();
    }

    // Method to handle the logic for adding a new category
    public function addCategory()
    {
        // Get the input data from the HTTP request and decode the JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and validate the input fields
        $categoryName = isset($data['categoryName']) ? trim(strip_tags($data['categoryName'])) : null;
        $description = isset($data['categoryDescription']) ? trim(strip_tags($data['categoryDescription'])) : null;
        $pageTitle = isset($data['categoryPageTitle']) ? trim(strip_tags($data['categoryPageTitle'])) : null;
        $pageDescription = isset($data['categoryPageDescription']) ? trim(strip_tags($data['categoryPageDescription'])) : null;

        // Check if any required fields are missing
        if (empty($categoryName) || empty($description) || empty($pageTitle) || empty($pageDescription)) 
        {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        // Generate a slug for the category name
        $categoryNameSlug = $this->slug->sluguer($categoryName);

        // Check if the category name already exists in the database
        if ($this->model->nameExist($categoryName)) 
        {
            return ["success" => false, "message" => "This name is already used"];
        }

        try 
        {
            // Save the new category to the database using the model
            $categoryId = $this->model->addCategory($categoryName, $description, $pageTitle, $pageDescription, $categoryNameSlug);

            // Prepare the new category data to return in the response
            $newCategory = [
                'id' => $categoryId,
                'name' => $categoryName,
                'description' => $description,
                'page_title' => $pageTitle,
                'page_description' => $pageDescription,
                'slug' => $categoryNameSlug,
            ];

            // Return a success response with the new category data
            return ["success" => true, "message" => "Category added successfully!!!", "category" => $newCategory];

        } 
        catch (\Exception $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
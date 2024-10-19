<?php

use Controllers\CategoriesManagement\AddCategoryController;
use Controllers\CategoriesManagement\CategoryController;
use Controllers\CategoriesManagement\DeleteCategoryController;
use Controllers\CategoriesManagement\UpdateCategoryController;

function categoryRoutes($adminAction, $authMiddleware) {

    $productCategory = new CategoryController();
    $updateCategory = new UpdateCategoryController();
    $addCategory = new AddCategoryController();
    $deleteCategory = new DeleteCategoryController();
    
    switch ($adminAction) 
    {
        case "getProductCategory":
            return $productCategory->getCategories();

        case "getCategoryById":
            return $updateCategory->getCategoryById();

        case "addCategory":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $addCategory->addCategory();
            }

        case "updateCategory":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $updateCategory->updateCategory();
            }

        case "deleteCategory":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $deleteCategory->deleteCategory();
            }

        default:
            return null;
    }
}
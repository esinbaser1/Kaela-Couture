<?php

use Controllers\CategoriesManagement\AddCategoryController;
use Controllers\CategoriesManagement\CategoryByIdController;
use Controllers\CategoriesManagement\CategoryController;
use Controllers\CategoriesManagement\DeleteCategoryController;
use Controllers\CategoriesManagement\UpdateCategoryController;

function handleCategoryRoutes($adminAction, $authMiddleware) {
    
    switch ($adminAction) {
        case "getProductCategory":
            $productCategory = new CategoryController();
            return $productCategory->getCategories();

        case "getCategoryById":
            $productCategoryById = new UpdateCategoryController();
            return $productCategoryById->getCategoryById();

        case "addCategory":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $addCategory = new AddCategoryController();
                return $addCategory->addCategory();
            }

        case "updateCategory":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $updateCategory = new UpdateCategoryController();
                return $updateCategory->updateCategory();
            }

        case "deleteCategory":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $deleteCategory = new DeleteCategoryController();
                return $deleteCategory->deleteCategory();
            }

        default:
            return null;
    }
}

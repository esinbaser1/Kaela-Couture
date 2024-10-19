<?php

use Controllers\ProductsManagement\AddProductController;
use Controllers\ProductsManagement\DeleteProductController;
use Controllers\ProductsManagement\ProductController;
use Controllers\ProductsManagement\UpdateProductController;

function productRoutes($adminAction, $authMiddleware) {
    
    $getProduct = new ProductController();
    $addProduct = new AddProductController();
    $updateProduct = new UpdateProductController();
    $deleteProduct = new DeleteProductController();
    
    switch ($adminAction) 
    {
        case "getProduct":
            return $getProduct->getProduct();
            
        case "getProductById":
            return $updateProduct->getProductById();
            
        case "addProduct":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $addProduct->addProduct();
            }

        case "updateProduct":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $updateProduct->updateProduct();
            }

        case "deleteProduct":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $deleteProduct->deleteProduct();
            }

        default:
            return null; 
    }
}

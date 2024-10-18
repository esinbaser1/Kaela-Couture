<?php

use Controllers\ProductsManagement\AddProductController;
use Controllers\ProductsManagement\DeleteProductController;
use Controllers\ProductsManagement\ProductController;
use Controllers\ProductsManagement\UpdateProductController;

function handleProductRoutes($adminAction, $authMiddleware) {
    
    switch ($adminAction) {
        case "getProduct":
            $getProduct = new ProductController();
            return $getProduct->getProduct();
            
        case "getProductById":
            $getProductById = new UpdateProductController();
            return $getProductById->getProductById();
            
        case "addProduct":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $addProduct = new AddProductController();
                return $addProduct->addProduct();
            }

        case "updateProduct":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $updateProduct = new UpdateProductController();
                return $updateProduct->updateProduct();
            }

        case "deleteProduct":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $deleteProduct = new DeleteProductController();
                return $deleteProduct->deleteProduct();
            }

        default:
            return null; 
    }
}

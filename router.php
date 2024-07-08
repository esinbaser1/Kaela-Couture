<?php

require_once('vendor/autoload.php');

use Models\Signup;
use Models\Login;
use Models\Contact;
use Models\AdminAddProduct;
use Models\AdminSelectProductModify;
use Models\AdminModifyProduct;

$action = $_REQUEST['action'] ?? null;

$response = ["success" => false, "message" => "Action not found"];

switch ($action) {
    case "signup":
        $signup = new Signup();
        $response = $signup->createUser();
        break;

    case "login":
        $signup = new Login();
        $response = $signup->getUser();
        break;

    case "contact":
        $contact = new Contact();
        $response = $contact->sendEmail();
        break;

    case "adminAddProduct":
        $adminAddProduct = new AdminAddProduct();
        $response = $adminAddProduct->addProduct();
        break;

    case "getCategorie":
        $adminAddProduct = new AdminAddProduct();
        $response = $adminAddProduct->getCategorie();
        break;

    case "adminSelectProductModify":
        $adminSelectProductModify = new AdminSelectProductModify();
        $response = $adminSelectProductModify->getProduct();
        break;

    case "getProduct":
        $productId = $_GET['productId'] ?? null;
        if ($productId) {
            $getProduct = new AdminModifyProduct();
            $response = $getProduct->getProductById($productId);
        } else {
            $response = ["success" => false, "message" => "Product ID missing"];
        }
        break;

    case "updateProduct":
        $productId = $_POST['productId'] ?? null;
        if ($productId) {
            $productName = $_POST['productName'] ?? '';
            $productDescription = $_POST['productDescription'] ?? '';
            $productCategory = $_POST['productCategory'] ?? '';
            $productImage = $_FILES['productImage'] ?? null;

            $adminUpdateProduct = new AdminModifyProduct();
            $response = $adminUpdateProduct->updateProduct($productId, $productName, $productDescription, $productCategory, $productImage);
        } else {
            $response = ["success" => false, "message" => "Product ID missing"];
        }
        break;
}

echo json_encode($response);

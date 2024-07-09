<?php

require_once('vendor/autoload.php');

use Models\Signup;
use Models\Login;
use Models\Contact;
use Models\AdminAddProduct;
use Models\AdminModifyProduct;
use Models\AdminProduct;
use Models\AdminDeleteProduct;
// use Models\AdminGetImage;
use Models\AdminInformation;
use Models\AdminAddInformation;

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

    default:
        $adminAction = $_REQUEST['adminAction'] ?? null;

        switch ($adminAction) {
            case "productDisplay":
                $productDisplay = new AdminProduct();
                $response = $productDisplay->getProductAndCategorie();
                break;

            case "addProduct":
                $adminAddProduct = new AdminAddProduct();
                $response = $adminAddProduct->addProduct();
                break;

            case "getCategorie":
                $adminAddProduct = new AdminAddProduct();
                $response = $adminAddProduct->getCategorie();
                break;

            case "getProduct":
                $getProduct = new AdminModifyProduct();
                $response = $getProduct->getProductById();
                break;

            case "updateProduct":
                $adminUpdateProduct = new AdminModifyProduct();
                $response = $adminUpdateProduct->updateProduct();
                break;

            case "deleteProduct":
                $adminDeleteProduct = new AdminDeleteProduct();
                $response = $adminDeleteProduct->deleteProduct();
                break;

            case "adminInformation":
                $adminInformation = new AdminInformation();
                $response = $adminInformation->getInformations();
                break;

            case "adminAddInformation":
                $adminAddInformation = new AdminAddInformation();
                $response = $adminAddInformation->addInformation();
                break;

            // case "getImage":
            //     $adminProduct = new AdminGetImage();
            //     $response = $adminProduct->getImage();
            //     break;

            default:
                $response = ["success" => false, "message" => "Admin action not found"];
                break;
        }
        break;
}

echo json_encode($response);

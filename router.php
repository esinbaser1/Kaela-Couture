<?php

require_once('vendor/autoload.php');

use Models\Signup;
use Models\Login;
use Models\Contact;

//Admin Products
use AdminProducts\AdminAddProduct;
use AdminProducts\AdminUpdateProduct;
use AdminProducts\AdminProduct;
use AdminProducts\AdminDeleteProduct;

//Admin Informations
use AdminInformations\AdminInformation;
use AdminInformations\AdminAddInformation;
use AdminInformations\AdminUpdateInformation;
use AdminInformations\AdminDeleteInformation;

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

                //PRODUCTS

            case "getProduct":
                $getProduct = new AdminProduct();
                $response = $getProduct->getProductAndCategorie();
                break;

            case "getProductById":
                $getProductById = new AdminUpdateProduct();
                $response = $getProductById->getProductById();
                break;

            case "getProductCategory":
                $adminAddProduct = new AdminAddProduct();
                $response = $adminAddProduct->getCategorie();
                break;

            case "addProduct":
                $adminAddProduct = new AdminAddProduct();
                $response = $adminAddProduct->addProduct();
                break;

            case "updateProduct":
                $adminUpdateProduct = new AdminUpdateProduct();
                $response = $adminUpdateProduct->updateProduct();
                break;

            case "deleteProduct":
                $adminDeleteProduct = new AdminDeleteProduct();
                $response = $adminDeleteProduct->deleteProduct();
                break;

                //INFORMATIONS

            case "getInformation":
                $getInformation = new AdminInformation();
                $response = $getInformation->getInformations();
                break;

            case "addInformation":
                $addInformation = new AdminAddInformation();
                $response = $addInformation->addInformation();
                break;

            case "getInformationById":
                $informationId = $_GET['informationId'] ?? null;
                $getInformationById = new AdminUpdateInformation();
                $response = $getInformationById->getInformationById($informationId);
                break;

            case "updateInformation":
                $adminUpdateInformation = new AdminUpdateInformation();
                $response = $adminUpdateInformation->updateInformation();
                break;

            case "deleteInformation":
                $informationId = $_GET['informationId'] ?? null;
                $adminDeleteInformation = new AdminDeleteInformation();
                $response = $adminDeleteInformation->deleteInformation($informationId);
                break;

            default:
                $response = ["success" => false, "message" => "Admin action not found"];
                break;
        }
        break;
}

echo json_encode($response);

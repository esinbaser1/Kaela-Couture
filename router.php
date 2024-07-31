<?php

require_once('vendor/autoload.php');

use Models\Signup;
use Models\Login;
use Models\Contact;
use Models\Token;

//Admin Products
use AdminProducts\AdminAddProduct;
use AdminProducts\AdminUpdateProduct;
use AdminProducts\AdminProduct;
use AdminProducts\AdminDeleteProduct;

//Admin Categories
use AdminCategories\AdminCategory;
use AdminCategories\AdminAddCategory;
use AdminCategories\AdminCategoryById;
use AdminCategories\AdminUpdateCategory;
use AdminCategories\AdminDeleteCategory;

//Admin Sections

use AdminSections\AdminSection;

//Admin Informations
use AdminInformations\AdminInformation;
use AdminInformations\AdminAddInformation;
use AdminInformations\AdminUpdateInformation;
use AdminInformations\AdminDeleteInformation;

//Admin Social Networks

use AdminSocialNetworks\AdminSocialNetwork;
use AdminSocialNetworks\AdminAddSocialNetwork;
use AdminSocialNetworks\AdminUpdateSocialNetwork;
use AdminSocialNetworks\AdminDeleteSocialNetwork;


$action = $_REQUEST['action'] ?? null;

$response = ["success" => false, "message" => "Action not found"];

switch ($action) {
    case "signup":
        $signup = new Signup();
        $response = $signup->createUser();
        break;

    case "login":
        $login = new Login();
        $response = $login->getUser();
        break;

        case "verifyToken":
            if ($token) {
                $tokenInstance = new Token();
                $response = $tokenInstance->verifyToken($token);
            } else {
                $response = ["success" => false, "message" => "Token not provided"];
            }
            break;

    case "contact":
        $contact = new Contact();
        $response = $contact->sendEmail();
        break;

    default:
        $adminAction = $_REQUEST['adminAction'] ?? null;

        switch ($adminAction) {

                // PRODUCTS

            case "getProduct":
                $getProduct = new AdminProduct();
                $response = $getProduct->getProductAndCategorie();
                break;

            case "getProductById":
                $getProductById = new AdminUpdateProduct();
                $response = $getProductById->getProductById();
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

                // CATEGORIES

            case "getProductCategory":
                $productCategory = new AdminCategory();
                $response = $productCategory->getCategorie();
                break;

            case "getCategoryById":
                $productCategoryById = new AdminCategoryById();
                $response = $productCategoryById->getCategoryById();
                break;

            case "addCategory":
                $addCategory = new AdminAddCategory();
                $response = $addCategory->addCategory();
                break;

            case "updateCategory":
                $updateCategory = new AdminUpdateCategory();
                $response = $updateCategory->updateCategory();
                break;

            case "deleteCategory":
                $deleteCategory = new AdminDeleteCategory();
                $response = $deleteCategory->deleteCategory();
                break;

                // SECTIONS

            case "getSection":
                $getSection = new AdminSection();
                $response = $getSection->getSection();
                break;

                // INFORMATIONS

            case "getInformation":
                $getInformation = new AdminInformation();
                $response = $getInformation->getInformations();
                break;

            case "addInformation":
                $addInformation = new AdminAddInformation();
                $response = $addInformation->addInformation();
                break;

            case "getInformationById":
                $getInformationById = new AdminUpdateInformation();
                $response = $getInformationById->getInformationById();
                break;

            case "updateInformation":
                $adminUpdateInformation = new AdminUpdateInformation();
                $response = $adminUpdateInformation->updateInformation();
                break;

            case "deleteInformation":
                $adminDeleteInformation = new AdminDeleteInformation();
                $response = $adminDeleteInformation->deleteInformation();
                break;

                // SOCIAL NETWORKS

            case "getSocialNetwork":
                $getSocialNetwork = new AdminSocialNetwork();
                $response = $getSocialNetwork->getSocialNetwork();
                break;

            case "getSocialNetworkById":
                // $socialNetworkId = $_GET['socialNetworkId'] ?? null;
                $getSocialNetworkById = new AdminUpdateSocialNetwork();
                $response = $getSocialNetworkById->getSocialNetworkById();
                break;

            case "addSocialNetwork":
                $addSocialNetwork = new AdminAddSocialNetwork();
                $response = $addSocialNetwork->addSocialNetwork();
                break;

            case "updateSocialNetwork":
                $updateSocialNetwork = new AdminUpdateSocialNetwork();
                $response = $updateSocialNetwork->updateSocialNetwork();
                break;

            case "deleteSocialNetwork":
                $deleteSocialNetwork = new AdminDeleteSocialNetwork();
                $response = $deleteSocialNetwork->deleteSocialNetwork();
                break;

            default:
                $response = ["success" => false, "message" => "Admin action not found"];
                break;
        }
        break;
}

echo json_encode($response);
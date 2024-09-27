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

//Admin Comments
use AdminComments\Comment;
use AdminComments\AddComment;


use Utils\AuthUtils;
$authMiddleware = new AuthUtils();

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

    case "contact":
        $contact = new Contact();
        $response = $contact->sendEmail();
        break;

    default:

        $adminAction = $_REQUEST['adminAction'] ?? null;
        switch ($adminAction) {

            case 'admin':
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                    $response = ["success" => true, "role" => "admin"];
                }
                break;

            // PRODUCTS

            case "getProduct":
                $getProduct = new AdminProduct();
                $response = $getProduct->getProduct();
                break;

            case "getProductById":
                $getProductById = new AdminUpdateProduct();
                $response = $getProductById->getProductById();
                break;

            case "addProduct":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $adminAddProduct = new AdminAddProduct();
                $response = $adminAddProduct->addProduct();
                }
                break;

            case "updateProduct":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $adminUpdateProduct = new AdminUpdateProduct();
                $response = $adminUpdateProduct->updateProduct();
                }
                break;

            case "deleteProduct":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $adminDeleteProduct = new AdminDeleteProduct();
                $response = $adminDeleteProduct->deleteProduct();
                }
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
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $addCategory = new AdminAddCategory();
                $response = $addCategory->addCategory();
                }
                break;

            case "updateCategory":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $updateCategory = new AdminUpdateCategory();
                $response = $updateCategory->updateCategory();
                }
                break;

            case "deleteCategory":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $deleteCategory = new AdminDeleteCategory();
                $response = $deleteCategory->deleteCategory();
                }
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
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $addInformation = new AdminAddInformation();
                $response = $addInformation->addInformation();
                }
                break;

            case "getInformationById":
                $getInformationById = new AdminUpdateInformation();
                $response = $getInformationById->getInformationById();
                break;

            case "updateInformation":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $adminUpdateInformation = new AdminUpdateInformation();
                $response = $adminUpdateInformation->updateInformation();
                }
                break;

            case "deleteInformation":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $adminDeleteInformation = new AdminDeleteInformation();
                $response = $adminDeleteInformation->deleteInformation();
                }
                break;

                // SOCIAL NETWORKS

            case "getSocialNetwork":
                $getSocialNetwork = new AdminSocialNetwork();
                $response = $getSocialNetwork->getSocialNetwork();
                break;

            case "getSocialNetworkById":
                $getSocialNetworkById = new AdminUpdateSocialNetwork();
                $response = $getSocialNetworkById->getSocialNetworkById();
                break;

            case "addSocialNetwork":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $addSocialNetwork = new AdminAddSocialNetwork();
                $response = $addSocialNetwork->addSocialNetwork();
                }
                break;

            case "updateSocialNetwork":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $updateSocialNetwork = new AdminUpdateSocialNetwork();
                $response = $updateSocialNetwork->updateSocialNetwork();
                }
                break;

            case "deleteSocialNetwork":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $deleteSocialNetwork = new AdminDeleteSocialNetwork();
                $response = $deleteSocialNetwork->deleteSocialNetwork();
                }
                break;

                // COMMENTS 
                case "getCommentsByProduct":
                    $productId = $_REQUEST['productDetailId'] ?? null;
                    if ($productId) {
                        $comment = new Comment();
                        $response = $comment->getCommentsByProduct($productId);
                    } else {
                        $response = ["success" => false, "message" => "Product ID not provided"];
                    }
                    break;
            
                case "addComment":
                    $addComment = new AddComment();
                    $response = $addComment->addComment();
                    break;

            default:
                $response = ["success" => false, "message" => "Admin action not found"];
                break;
        }
        break;
}

echo json_encode($response);

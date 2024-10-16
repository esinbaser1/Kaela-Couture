<?php

require_once('vendor/autoload.php');

use Models\SignupModel;
use Models\LoginModel;
use Models\ContactModel;

//Admin Products
use AdminProducts\AddProductModel;
use AdminProducts\UpdateProductModel;
use AdminProducts\ProductModel;
use AdminProducts\DeleteProductModel;

//Admin Categories
use AdminCategories\CategoryModel;
use AdminCategories\AddCategoryModel;
use AdminCategories\CategoryByIdModel;
use AdminCategories\UpdateCategoryModel;
use AdminCategories\DeleteCategoryModel;

//Admin Sections

use AdminSections\SectionModel;

//Admin Informations
use AdminInformations\InformationModel;
use AdminInformations\AddInformationModel;
use AdminInformations\UpdateInformationModel;
use AdminInformations\DeleteInformationModel;

//Admin Social Networks

use AdminSocialNetworks\SocialNetworkModel;
use AdminSocialNetworks\AddSocialNetworkModel;
use AdminSocialNetworks\UpdateSocialNetworkModel;
use AdminSocialNetworks\DeleteSocialNetworkModel;

//Admin Comments
use AdminComments\CommentModel;
use AdminComments\AddCommentModel;


use Utils\AuthUtils;
$authMiddleware = new AuthUtils();

$action = $_REQUEST['action'] ?? null;

$response = ["success" => false, "message" => "Action not found"];

switch ($action) {

    case "signup":
        $signup = new SignupModel();
        $response = $signup->createUser();
        break;

    case "login":
        $login = new LoginModel();
        $response = $login->getUser();
        break;

    case "contact":
        $contact = new ContactModel();
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
                $getProduct = new ProductModel();
                $response = $getProduct->getProduct();
                break;

            case "getProductById":
                $getProductById = new UpdateProductModel();
                $response = $getProductById->getProductById();
                break;

            case "addProduct":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $addProduct = new AddProductModel();
                $response = $addProduct->addProduct();
                }
                break;

            case "updateProduct":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $updateProduct = new UpdateProductModel();
                $response = $updateProduct->updateProduct();
                }
                break;

            case "deleteProduct":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $deleteProduct = new DeleteProductModel();
                $response = $deleteProduct->deleteProduct();
                }
                break;

                // CATEGORIES

            case "getProductCategory":
                $productCategory = new CategoryModel();
                $response = $productCategory->getCategorie();
                break;

            case "getCategoryById":
                $productCategoryById = new CategoryByIdModel();
                $response = $productCategoryById->getCategoryById();
                break;

            case "addCategory":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $addCategory = new AddCategoryModel();
                $response = $addCategory->addCategory();
                }
                break;

            case "updateCategory":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $updateCategory = new UpdateCategoryModel();
                $response = $updateCategory->updateCategory();
                }
                break;

            case "deleteCategory":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $deleteCategory = new DeleteCategoryModel();
                $response = $deleteCategory->deleteCategory();
                }
                break;

                // SECTIONS

            case "getSection":
                $getSection = new SectionModel();
                $response = $getSection->getSection();
                break;

                // INFORMATIONS

            case "getInformation":
                $getInformation = new InformationModel();
                $response = $getInformation->getInformations();
                break;

            case "addInformation":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $addInformation = new AddInformationModel();
                $response = $addInformation->addInformation();
                }
                break;

            case "getInformationById":
                $getInformationById = new UpdateInformationModel();
                $response = $getInformationById->getInformationById();
                break;

            case "updateInformation":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $updateInformation = new UpdateInformationModel();
                $response = $updateInformation->updateInformation();
                }
                break;

            case "deleteInformation":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $deleteInformation = new DeleteInformationModel();
                $response = $deleteInformation->deleteInformation();
                }
                break;

                // SOCIAL NETWORKS

            case "getSocialNetwork":
                $getSocialNetwork = new SocialNetworkModel();
                $response = $getSocialNetwork->getSocialNetwork();
                break;

            case "getSocialNetworkById":
                $getSocialNetworkById = new UpdateSocialNetworkModel();
                $response = $getSocialNetworkById->getSocialNetworkById();
                break;

            case "addSocialNetwork":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $addSocialNetwork = new AddSocialNetworkModel();
                $response = $addSocialNetwork->addSocialNetwork();
                }
                break;

            case "updateSocialNetwork":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $updateSocialNetwork = new UpdateSocialNetworkModel();
                $response = $updateSocialNetwork->updateSocialNetwork();
                }
                break;

            case "deleteSocialNetwork":
                $authResult = $authMiddleware->verifyAccess('admin');
                if ($authResult !== null) {
                    $response = $authResult;
                } else {
                $deleteSocialNetwork = new DeleteSocialNetworkModel();
                $response = $deleteSocialNetwork->deleteSocialNetwork();
                }
                break;

                // COMMENTS 
                case "getCommentsByProduct":
                    $productId = $_REQUEST['productDetailId'] ?? null;
                    if ($productId) {
                        $comment = new CommentModel();
                        $response = $comment->getCommentsByProduct($productId);
                    } else {
                        $response = ["success" => false, "message" => "Product ID not provided"];
                    }
                    break;
            
                case "addComment":
                    $addComment = new AddCommentModel();
                    $response = $addComment->addComment();
                    break;

            default:
                $response = ["success" => false, "message" => "Admin action not found"];
                break;
        }
        break;
}

echo json_encode($response);

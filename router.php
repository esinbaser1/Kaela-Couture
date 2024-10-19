<?php

require_once 'vendor/autoload.php';

require_once 'routes/productRoutes.php';
require_once 'routes/categoryRoutes.php';
require_once 'routes/informationRoutes.php';
require_once 'routes/socialNetworkRoutes.php';
require_once 'routes/sectionRoutes.php';

use Controllers\CommentsManagement\AddCommentController;
use Controllers\CommentsManagement\CommentController;
use Controllers\ContactController;
use Controllers\LoginController;
use Controllers\SignupController;
use Utils\AuthUtils;

$authMiddleware = new AuthUtils();

$signup = new SignupController();
$login = new LoginController();
$contact = new ContactController();
$comment = new CommentController();
$addComment = new AddCommentController();

$action = $_REQUEST['action'] ?? null;
$response = ["success" => false, "message" => "Action not found"];

// Management of non-admin actions
switch ($action) 
{
    case "signup":
        $response = $signup->signup();
        break;

    case "login":
        $response = $login->login();
        break;

    case "contact":
        $response = $contact->sendEmail();
        break;

    case "getCommentsByProduct":
        $productId = $_REQUEST['productDetailId'] ?? null;
        if ($productId) 
        {
            $response = $comment->getCommentsByProduct($productId);
        } else 
        {
            $response = ["success" => false, "message" => "Product ID not provided"];
        }
        break;

    case "addComment":
        $response = $addComment->addComment();
        break;

    // Managing admin actions
    default:
        $adminAction = $_REQUEST['adminAction'] ?? null;

        if ($adminAction) 
        {
            $response = productRoutes($adminAction, $authMiddleware) ??
                categoryRoutes($adminAction, $authMiddleware) ??
                informationRoutes($adminAction, $authMiddleware) ??
                socialNetworkRoutes($adminAction, $authMiddleware) ??
                sectionRoutes($adminAction);
        }
        break;
}

echo json_encode($response);
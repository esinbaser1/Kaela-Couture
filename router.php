<?php

require_once('vendor/autoload.php');

use Models\Signup;
use Models\Login;
use Models\Contact;
use Models\AdminAddProduct;

$action = $_REQUEST['action'] ?? null;

$response = ["success" => false, "message" => "Action not found"];

switch($action) 
{
    case "signup" : 
        $signup = new Signup();
        $response = $signup->createUser();
    break;

    case "login" : 
        $signup = new Login();
        $response = $signup->getUser();
    break;

    case "contact" : 
        $contact = new Contact();
        $response = $contact->sendEmail();
    break;

    case "adminAddProduct" :
        $adminAddProduct = new AdminAddProduct();
        $response = $adminAddProduct->addProduct();
    break;

    case "getCategorie" :
        $adminAddProduct = new AdminAddProduct();
        $response = $adminAddProduct->getCategorie();
    break;

}

echo json_encode($response);
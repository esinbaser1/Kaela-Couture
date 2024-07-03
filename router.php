<?php

require_once('vendor/autoload.php');

use Models\Signup;

$action = $_REQUEST['action'] ?? null;

$response = ["success" => false, "message" => "Action not found"];

switch($action) 
{
    case "signup" : 
        $signup = new Signup();
        $response = $signup->createUser();
    break;

  
}

echo json_encode($response);
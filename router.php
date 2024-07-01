<?php

require_once('vendor/autoload.php');

$action = $_REQUEST['action'] ?? NULL;

switch($action) 
{
    default :
        echo "default page";
    break;
}
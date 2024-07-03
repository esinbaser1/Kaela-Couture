<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

const IMG = "assets/img/";

if($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    require_once "router.php";
}
<?php

header("Access-Control-Allow-Origin: *"); // a changer
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json"); //Spécifie que la réponse du serveur sera au format JSON.
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'; frame-ancestors 'none'; base-uri 'self';");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') 
{
    http_response_code(200);
    exit();
}

const IMG = "assets/img/";

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') 
{
    require_once "router.php";
}
else 
{
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

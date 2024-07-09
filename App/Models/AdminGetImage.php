<?php

// namespace Models;

// use App\Database;

// class AdminGetImage
// {
//     protected $db;

//     public function __construct()
//     {
//         $database = new Database();
//         $this->db = $database->getConnection();
//     }

//     public function getImage()
//     {
//         try 
//         {
//             $request = "SELECT * FROM image";
//             $pdo = $this->db->query($request);
//             $images = $pdo->fetchAll();
            
//             return ["success" => true, "images" => $images];
//         }
//         catch(\PDOException $e)
//         {
//             error_log("Error when retrieving images: " . $e->getMessage());

//             return ["success" => false, "message" => "Database error"];
//         }
//     }
// }

<?php

namespace Models;

use App\Database;

class AdminInformation
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getInformations()
    {
        try {
            $request = "SELECT * FROM about_me";
            $pdo = $this->db->query($request);
            $information = $pdo->fetchAll();

            return ["success" => true, "information" => $information, "message" => "Information retrieved successfully!"];

        } catch (\PDOException $e) {
            error_log("Error when retrieving information: " . $e->getMessage());
            
            return ["success" => false, "message" => "Database error"];
        }
    }
}

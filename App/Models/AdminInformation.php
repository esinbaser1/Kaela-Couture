<?php

namespace Models;

use App\Database;

class AdminInformation
{
    protected $db;
    protected $slug;

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

            return ["success" => true, "information" => $information];

        } 
        catch (\PDOException $e) 
        {
            error_log("Error when retrieving images: " . $e->getMessage());

            return ["success" => false, "message" => "Database error"];
        }
    }
}

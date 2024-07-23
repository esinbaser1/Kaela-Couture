<?php

namespace AdminSections;

use App\Database;

class AdminSection 
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getSection()
    {
        try
        {
            $request = "SELECT * FROM section";
            $pdo = $this->db->query($request);
            $section = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            return ["success" => true, "section" => $section];
        }
        catch(\PDOException $e)
        {
            error_log("Error when retrieving categories: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

}
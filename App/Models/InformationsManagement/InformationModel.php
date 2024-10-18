<?php

namespace Models\InformationsManagement;

use App\Database;

class InformationModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to retrieve all information from the database
    public function getInformations()
    {
        try 
        {
            // SQL query to select all data
            $request = "SELECT * FROM information";
            $pdo = $this->db->query($request);
            return $pdo->fetchAll(\PDO::FETCH_ASSOC);

        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}
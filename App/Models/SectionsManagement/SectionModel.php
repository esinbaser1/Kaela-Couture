<?php

namespace Models\SectionsManagement;

use App\Database;

// Class to handle the retrieval of sections in the admin panel
class SectionModel 
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

     // Method to retrieve all sections from the database
    public function getSection()
    {
        try
        {
            // SQL query to select all sections from the 'sections' table
            $request = "SELECT * FROM section";
            $pdo = $this->db->query($request);
            $section = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            // Return the list of categories with a success response
            return ["success" => true, "section" => $section];
        }
        catch(\PDOException $e)
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }

}
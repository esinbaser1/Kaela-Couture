<?php

namespace AdminInformations;

use App\Database;

// Class to handle retrieving information in the admin panel
class AdminInformation
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
            $information = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            // Return a success response with the retrieved information
            return ["success" => true, "information" => $information];

        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

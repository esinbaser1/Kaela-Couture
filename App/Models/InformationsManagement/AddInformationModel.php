<?php

namespace Models\InformationsManagement;

use App\Database;

class AddInformationModel
{
    protected $db; 

    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection();
    }

    // Method to add information to the database
    public function insertInformation($description, $mobile, $address)
    {
        try
        {
            // SQL query to insert new information
            $request = "INSERT INTO information (description, mobile, address) VALUES (?, ?, ?)";
            $pdo = $this->db->prepare($request); 
            $pdo->execute([$description, $mobile, $address]);

            // Get the ID of the inserted information
            return $this->db->lastInsertId();
        } 
        catch (\PDOException $e) 
        {
            // Throw an exception in case of a database error
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
<?php

namespace Models\InformationsManagement;

use App\Database;

class UpdateInformationModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to fetch information by its ID
    public function getInformationById($informationId)
    {
        // SQL query to select the information by ID
        $request = "SELECT * FROM information WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$informationId]);
        return $pdo->fetch(\PDO::FETCH_ASSOC); // Return the raw data from the database
    }

    // Method to update existing information in the database
    public function updateInformation($informationId, $description, $mobile, $email, $address)
    {
        try 
        {
            $request = "UPDATE information SET description = ?, mobile = ?, email = ?, address = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$description, $mobile, $email, $address, $informationId]);

            // Return the number of affected rows
            return $pdo->rowCount() > 0;
        }
        catch (\PDOException $e) 
        {
            // Throw an exception to be handled by the controller
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

}
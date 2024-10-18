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
    public function updateInformation($informationId, $description, $mobile, $address)
    {
        $request = "UPDATE information SET description = ?, mobile = ?, address = ? WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$description, $mobile, $address, $informationId]);

        return $pdo->rowCount(); // Return the number of affected rows
    }
}
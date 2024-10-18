<?php
namespace Models\InformationsManagement;

use App\Database;

class DeleteInformationModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    // Method to delete information by its ID
    public function deleteInformationById($informationId)
    {
        try 
        {
            // SQL query to delete the information by its ID
            $request = "DELETE FROM information WHERE id = ?";
            $pdo = $this->db->prepare($request); 
            $pdo->execute([$informationId]);

            // Check if any rows were affected by the deletion
            return $pdo->rowCount() > 0;
        } 
        catch (\PDOException $e) 
        {
            // Throw an exception in case of a database error
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
<?php
namespace InformationsManagement;

use App\Database;

// Class to handle the deletion of information
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
    public function deleteInformation()
    {
        // Retrieve the input data from the HTTP request and decode it from JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the information ID from the input data
        $informationId = isset($data['informationId']) ? strip_tags($data['informationId']) : null;

        // Check if the information ID is missing
        if (empty($informationId)) 
        {
            return ["success" => false, "message" => "Information ID missing"];
        }

        try 
        {
            // SQL query to delete the information from by its ID
            $request = "DELETE FROM information WHERE id = ?";
            $pdo = $this->db->prepare($request); 
            $pdo->execute([$informationId]);

            // Check if any rows were affected 
            if ($pdo->rowCount() > 0) 
            {
                // Success response if the information was deleted
                return ["success" => true, "message" => "Information deleted successfully"]; 
            } 
            else 
            {
                // Error response if no information was found with that ID
                return ["success" => false, "message" => "Information not found"];
            }
        }
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

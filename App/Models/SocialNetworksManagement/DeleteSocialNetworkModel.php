<?php

namespace SocialNetworksManagement;

use App\Database;

// Class to handle the deletion of social network entries in the admin panel
class DeleteSocialNetworkModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to delete a social network entry from the database
    public function deleteSocialNetwork()
    {
        // Retrieve the input data from the HTTP request (JSON format)
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and get the social network ID from the input data
        $socialNetworkId = isset($data['socialNetworkId']) ? strip_tags($data['socialNetworkId']) : null;

        // Check if the social network ID is provided
        if (empty($socialNetworkId)) 
        {
            return ["success" => false, "message" => "Social network ID missing"]; // Return an error if the ID is missing
        }
        try 
        {
            // SQL query to delete the social network from the database using its ID
            $request = "DELETE FROM social_network WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$socialNetworkId]);

            // Check if the deletion was successful
            if ($pdo->rowCount() > 0) 
            {
                // Return success message if deleted
                return ["success" => true, "message" => "Social network deleted successfully"];
            } 
            else 
            {
                // Return error if no record was found with the given ID
                return ["success" => false, "message" => "Social network not found"]; 
            }
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}
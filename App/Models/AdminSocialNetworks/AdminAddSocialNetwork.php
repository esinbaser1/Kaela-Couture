<?php

namespace AdminSocialNetworks;

use App\Database;

// Class to handle adding a new social network to the admin panel
class AdminAddSocialNetwork
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to add a new social network entry to the database
    public function addSocialNetwork()
    {
        // Get the input data from the request body (JSON format)
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the platform and URL from the input
        $platform = isset($data['platform']) ? trim(strip_tags($data['platform'])) : null;
        $url = isset($data['url']) ? trim(strip_tags($data['url'])) : null;

        // Check if any required fields are missing
        if (empty($platform) || empty($url)) 
        {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        try 
        {
            // SQL query to insert the new social network into the database
            $request = "INSERT INTO social_network (platform, url) VALUES (?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$platform, $url]);

            // Get the ID of the newly inserted social network
            $id = $this->db->lastInsertId();
            
            // Prepare the newly added social network data for the response
            $newSocialNetwork = [
                'id' => $id,
                'platform' => $platform,
                'url' => $url,
            ];

            // Return success response with the new social network data
            return ["success" => true, "message" => "Social network added successfully!!!", "socialNetwork" => $newSocialNetwork];

        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

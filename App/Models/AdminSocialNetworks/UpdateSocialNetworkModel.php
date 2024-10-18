<?php

namespace AdminSocialNetworks;

use App\Database;

// Class responsible for handling the update of social network entries in the admin panel
class UpdateSocialNetworkModel
{
    protected $db;

    // Constructor: Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Private method to retrieve a social network by ID
    private function fetchSocialNetworkById($socialNetworkId)
    {
        $request = "SELECT * FROM social_network WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$socialNetworkId]);
        return $pdo->fetch(\PDO::FETCH_ASSOC);
    }

    // Method to retrieve a social network entry by its ID
    public function getSocialNetworkById()
    {
        $socialNetworkId = $_GET['socialNetworkId'] ?? null;

        if (empty($socialNetworkId)) 
        {
            return ["success" => false, "message" => "Social network ID missing"];
        }

        $socialNetwork = $this->fetchSocialNetworkById($socialNetworkId);

        if ($socialNetwork) 
        {
            return ["success" => true, "socialNetwork" => $socialNetwork];
        } 
        else 
        {
            return ["success" => false, "message" => "Social network not found"];
        }
    }

    // Method to update a social network entry
    public function updateSocialNetwork()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $socialNetworkId = isset($data['id']) ? trim(strip_tags($data['id'])) : null;
        $platform = isset($data['platform']) ? trim(strip_tags($data['platform'])) : null;
        $url = isset($data['url']) ? trim(strip_tags($data['url'])) : null;

        if (empty($socialNetworkId) || empty($platform) || empty($url)) 
        {
            return ["success" => false, "message" => "All fields must be filled"];
        }

         // Checks if the url is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) 
        {
            return ["success" => false, "message" => "Invalid url."];
        }

        // Fetch the current social network data from the database
        $existingSocialNetwork = $this->fetchSocialNetworkById($socialNetworkId);

        // Check if any changes were made to the social network data
        if (
            $platform == $existingSocialNetwork['platform'] &&
            $url == $existingSocialNetwork['url']
        ) 
        {
            return ["success" => false, "message" => "No changes detected"];
        }

        try 
        {
            // SQL query to update the social network entry
            $request = "UPDATE social_network SET platform = ?, url = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$platform, $url, $socialNetworkId]);

            // Return success response with the updated social network data
            return ["success" => true, "message" => "Social network updated successfully!!", "socialNetwork" => [
                'id' => $socialNetworkId,
                'platform' => $platform,
                'url' => $url,
            ]];
        } 
        catch (\PDOException $e) 
        {
            // Return a failure message
            return ["success" => false, "message" => "Database error"];
        }
    }
}
<?php

namespace Models\SocialNetworksManagement;

use App\Database;

// Class to handle the retrieval of social networks in the admin panel
class SocialNetworkModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to retrieve all social networks from the database
    public function getSocialNetwork()
    {
        try 
        {
            // SQL query to select all social networks 
            $request = "SELECT * FROM social_network";
            $pdo = $this->db->query($request);
            $socialNetworks = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            // Return the list of social networks with a success response
            return ["success" => true, "socialNetworks" => $socialNetworks];
            
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}
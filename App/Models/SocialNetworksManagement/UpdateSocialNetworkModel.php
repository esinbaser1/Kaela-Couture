<?php

namespace Models\SocialNetworksManagement;

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

    // Method to retrieve a social network entry by its ID
    public function getSocialNetworkById($socialNetworkId)
    {
        $request = "SELECT * FROM social_network WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$socialNetworkId]);
        return $pdo->fetch(\PDO::FETCH_ASSOC); // Return raw data from the database
    }

    // Method to update a social network entry
    public function updateSocialNetwork($socialNetworkId, $platform, $url)
    {
        $request = "UPDATE social_network SET platform = ?, url = ? WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$platform, $url, $socialNetworkId]);

        return $pdo->rowCount(); // Return the number of affected rows
    }
}
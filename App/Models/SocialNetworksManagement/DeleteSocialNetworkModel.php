<?php

namespace Models\SocialNetworksManagement;

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
    public function deleteSocialNetwork($socialNetworkId)
    {
        $request = "DELETE FROM social_network WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$socialNetworkId]);

        return $pdo->rowCount(); // Return the number of affected rows
    }
}
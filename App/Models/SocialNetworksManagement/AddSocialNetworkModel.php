<?php

namespace Models\SocialNetworksManagement;

use App\Database;

// Class to handle adding a new social network to the admin panel
class AddSocialNetworkModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to insert a new social network into the database
    public function insertSocialNetwork($platform, $url)
    {
        $request = "INSERT INTO social_network (platform, url) VALUES (?, ?)";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$platform, $url]);

        // Return the ID of the newly inserted social network
        return $this->db->lastInsertId();
    }
        // Check if a platform name and url already exists in the database
        public function existsInColumn($column, $value)
        {
            $query = "SELECT COUNT(*) FROM social_network WHERE $column = ?";
            $pdo = $this->db->prepare($query);
            $pdo->execute([$value]);
            return $pdo->fetchColumn() > 0;
        }
}

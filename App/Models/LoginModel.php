<?php

namespace Models;

use App\Database;

class LoginModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection(); 
    }

    // Method to retrieve the user by email from the database
    public function getUserByEmail($email)
    {
        // Prepare an SQL query to search for the user by email
        $request = "SELECT * FROM user WHERE email = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$email]);

        return $pdo->fetch(\PDO::FETCH_ASSOC); // Return the user data
    }
}
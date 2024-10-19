<?php

namespace Models;

use App\Database;

class SignupModel 
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to insert a new user into the database
    public function addUser($username, $email, $passwordHash)
    {
        // Insert the user data into the database
        $request = "INSERT INTO user (username, email, password, last_active_at) VALUES (?,?,?, NOW())";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$username, $email, $passwordHash]);

        return $this->db->lastInsertId(); // Return the newly inserted user ID
    }

    // function that checks if a name already exists
    public function existsInColumn($column, $value)
    {
        $request = "SELECT COUNT(*) FROM user WHERE $column = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$value]);
        return $pdo->fetchColumn() > 0;
    }
}
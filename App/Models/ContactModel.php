<?php

namespace Models;

use App\Database;

class ContactModel 
{
    protected $db;

    // Initializes the database connection 
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection(); 
    }

    // Method to insert contact information into the database
    public function addContactMessage($email, $object, $message, $userId)
    {
        // SQL query to insert the contact message into the database
        $request = "INSERT INTO contact (email, object, message, user_id) VALUES (?,?,?,?)";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$email, $object, $message, $userId]);

        return $pdo->rowCount(); // Return the number of affected rows
    }

    // Method to retrieve user's email by their user ID
    public function getUserEmailById($userId)
    {
        // SQL query to retrieve the user's email from the database
        $request = "SELECT email FROM user WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$userId]);

        return $pdo->fetchColumn(); // Return the user's email
    }
}

<?php

namespace Models\CommentsManagement;

use App\Database;

// Class to handle adding a new comment
class AddCommentModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to add a new comment in the database
    public function addComment($content, $userId, $productId)
    {
        try 
        {
            // SQL query to insert the new comment into the 'comment' table
            $request = "INSERT INTO comment (content, user_id, product_id) VALUES (?, ?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$content, $userId, $productId]);

            // Get the ID of the newly inserted comment
            return $this->db->lastInsertId();

        } 
        catch (\PDOException $e) 
        {
            // Throw an exception to be handled by the controller
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    // Method to fetch the username by user ID
    public function getUsernameById($userId)
    {
        $requestUser = "SELECT username FROM user WHERE id = ?";
        $pdo = $this->db->prepare($requestUser);
        $pdo->execute([$userId]);
        return $pdo->fetch(\PDO::FETCH_ASSOC);
    }
}
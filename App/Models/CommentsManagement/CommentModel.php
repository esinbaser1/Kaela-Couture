<?php

namespace CommentsManagement;

use App\Database;

// Class to manage comments, specifically for retrieving comments by product ID
class CommentModel
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to retrieve all comments for a specific product using its product ID
    public function getCommentsByProduct($productId)
    {
        try 
        {
            // SQL query to select comments for the specified product ID
            // The query joins the 'comment' table with the 'user' table to get the username
            // Uses COALESCE to return 'Deleted user' (User deleted) if the user has been deleted
            $request = "SELECT 
            comment.*, 
            COALESCE(user.username, 'Deleted user') AS username, 
            comment.user_id 
            FROM comment 
            LEFT JOIN user ON comment.user_id = user.id 
            WHERE comment.product_id = ?"; 
            
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]); 
            $comments = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            // Return a success response with the comments
            return ["success" => true, "comments" => $comments];

        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

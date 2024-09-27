<?php

namespace AdminComments;

use App\Database;

// Class to handle adding a new comment
class AddComment
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to add a new comment
    public function addComment()
    {
        // Get the input data from the HTTP request and decode the JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and validate the input data
        $content = isset($data['content']) ? strip_tags($data['content']) : null;
        $userId = isset($data['userId']) ? strip_tags($data['userId']) : null;
        $productId = isset($data['productId']) ? strip_tags($data['productId']) : null;

        // Check if any required fields are missing
        if (!$content || !$userId || !$productId) 
        {
            return ["success" => false, "message" => "Missing required fields"];
        }

        try 
        {
            // Begin a database transaction
            $this->db->beginTransaction();

            // SQL query to insert the new comment into the 'comment' table
            $request = "INSERT INTO comment (content, user_id, product_id) VALUES (?, ?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$content, $userId, $productId]);

            // Get the ID of the newly inserted comment
            $id = $this->db->lastInsertId();

            // Fetch the username of the user who added the comment
            $requestUser = "SELECT username FROM user WHERE id = ?";
            $pdo = $this->db->prepare($requestUser);
            $pdo->execute([$userId]);
            $user = $pdo->fetch(\PDO::FETCH_ASSOC);

            // Commit the transaction
            $this->db->commit();

            // Prepare the comment data to return in the response
            $comment = [
                'id' => $id,
                'content' => $content,
                'user_id' => $userId,
                'product_id' => $productId,
                'username' => $user['username']
            ];

            // Return a success response with the comment data
            return ["success" => true, "message" => "Comment added successfully!", "comment" => $comment];

        } 
        catch (\PDOException $e) 
        {
            // Roll back the transaction if an error occurs
            $this->db->rollBack();
            // Return a failure response
            return ["success" => false, "message" => "Failed to add comment due to a database error. Please try again."];
        }
    }
}

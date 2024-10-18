<?php

namespace Controllers\CommentsManagement;

use Models\CommentsManagement\AddCommentModel;

class AddCommentController
{
    protected $model;

    // Initializes the model
    public function __construct()
    {
        $this->model = new AddCommentModel();
    }

    // Method to handle the logic for adding a new comment
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
            // Save the comment using the model
            $commentId = $this->model->addComment($content, $userId, $productId);

            // Fetch the username of the user who added the comment
            $user = $this->model->getUsernameById($userId);

            // Prepare the comment data to return in the response
            $comment = [
                'id' => $commentId,
                'content' => $content,
                'user_id' => $userId,
                'product_id' => $productId,
                'username' => $user['username']
            ];

            // Return a success response with the comment data
            return ["success" => true, "message" => "Comment added successfully!", "comment" => $comment];

        } 
        catch (\Exception $e) 
        {
            // Return a failure response if an error occurs
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
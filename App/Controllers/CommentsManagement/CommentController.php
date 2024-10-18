<?php

namespace Controllers\CommentsManagement;

use Models\CommentsManagement\CommentModel;

class CommentController 
{
    protected $model;

    // Initializes the model
    public function __construct()
    {
        $this->model = new CommentModel();
    }

    // Method to handle the retrieval of comments for a specific product
    public function getCommentsByProduct($productId)
    {
        // Validate the product ID
        if (empty($productId)) {
            return ["success" => false, "message" => "Product ID is missing"];
        }

        try {
            // Fetch the comments using the model
            $comments = $this->model->fetchCommentsByProduct($productId);

            // Return a success response with the comments
            return ["success" => true, "comments" => $comments];

        } catch (\Exception $e) {
            // Return a failure response in case of error
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
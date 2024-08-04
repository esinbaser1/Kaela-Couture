<?php

namespace AdminComments;

use App\Database;

class Comment
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getCommentsByProduct($productId)
    {
        try {
            $request = "SELECT comment.*, user.username AS username, user.id AS user_id 
                        FROM comment 
                        JOIN user ON comment.user_id = user.id 
                        WHERE comment.product_id = ?"; 
            $pdo = $this->db->prepare($request);
            $pdo->execute([$productId]);
            $comments = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            return ["success" => true, "comments" => $comments];
        } catch (\PDOException $e) {
            error_log("Error when retrieving comments: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}


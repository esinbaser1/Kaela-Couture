<?php

namespace AdminComments;

use App\Database;

class AddComment
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addComment()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $content = isset($data['content']) ? strip_tags($data['content']) : null;
        $userId = isset($data['userId']) ? strip_tags($data['userId']) : null;
        $productId = isset($data['productId']) ? strip_tags($data['productId']) : null;

        if (!$content || !$userId || !$productId) {
            return ["success" => false, "message" => "Missing required fields"];
        }

        try {
            $this->db->beginTransaction();

            $request = "INSERT INTO comment (content, user_id, product_id) VALUES (?, ?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$content, $userId, $productId]);

            $id = $this->db->lastInsertId();

            $requestUser = "SELECT username FROM user WHERE id = ?";
            $pdo = $this->db->prepare($requestUser);
            $pdo->execute([$userId]);
            $user = $pdo->fetch(\PDO::FETCH_ASSOC);

            $this->db->commit();

            $comment = [
                'id' => $id,
                'content' => $content,
                'user_id' => $userId,
                'product_id' => $productId,
                'username' => $user['username']
            ];

            return ["success" => true, "message" => "Comment added successfully!", "comment" => $comment];

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error when adding comment: " . $e->getMessage());
            return ["success" => false, "message" => "Failed to add comment due to a database error. Please try again."];
        }
    }
}

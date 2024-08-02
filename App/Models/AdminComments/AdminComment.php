<?php

namespace AdminComments;

use App\Database;

class AdminComment
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getComment()
    {
        try {

            $request = "SELECT comment.*, user.username AS username FROM comment JOIN user ON comment.user_id = user.id"; //me permet d'afficher le pseudo et le commentaires en même temps
            $pdo = $this->db->query($request);
            $comment = $pdo->fetchAll(\PDO::FETCH_ASSOC);

            return ["success" => true, "comment" => $comment];
        } catch (\PDOException $e) {
            error_log("Error when retrieving categories: " . $e->getMessage());

            return ["success" => false, "message" => "Database error"];
        }
    }
}

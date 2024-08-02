<?php

namespace AdminComments;

use App\Database;

class AdminAddComment
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
            $this->db->beginTransaction(); // Assure que l'insertion du commentaire et la récupération du nom d'utilisateur se produisent ensemble. Si une des opérations échoue, aucune des deux ne sera appliquée

            // Insérer le commentaire
            $request = "INSERT INTO comment (content, user_id, product_id) VALUES (?, ?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$content, $userId, $productId]);

            $id = $this->db->lastInsertId();

            // Récupérer le nom d'utilisateur
            $requestUser = "SELECT username FROM user WHERE id = ?";
            $pdo = $this->db->prepare($requestUser);
            $pdo->execute([$userId]);
            $user = $pdo->fetch(\PDO::FETCH_ASSOC);

            $this->db->commit(); // Applique toutes les opérations de la transaction à la bdd, les opérations sont définitivement enregistrées

            $comment = [
                'id' => $id,
                'content' => $content,
                'user_id' => $userId,
                'product_id' => $productId,
                'username' => $user['username']
            ];

            return ["success" => true, "message" => "Comment added successfully!!!", "comment" => $comment];

        } catch (\PDOException $e) {
            $this->db->rollBack(); // Annule toutes les opérations effectuées dans la transaction si une erreur survient, ramenant la bdd à l'état dans lequel elle se trouvait avant le début de la transaction
            error_log("Error when adding comment: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

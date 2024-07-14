<?php

namespace Models;

use App\Database;

class AdminDeleteInformation
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function deleteInformation($informationId)
    {
        // Vérification de l'ID de l'information
        $informationId = strip_tags($informationId);

        if (empty($informationId)) {
            return ["success" => false, "message" => "Information ID missing"];
        }

        try {
            $request = "DELETE FROM about_me WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$informationId]);

            if ($pdo->rowCount() > 0) {
                return ["success" => true, "message" => "Information deleted successfully"];
            } else {
                return ["success" => false, "message" => "Information not found"];
            }
        } catch (\PDOException $e) {
            error_log("Error when deleting information: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

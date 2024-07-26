<?php

namespace AdminSocialNetworks;

use App\Database;

class AdminDeleteSocialNetwork
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function deleteSocialNetwork()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $socialNetworkId = isset($data['socialNetworkId']) ? strip_tags($data['socialNetworkId']) : null;

        if (empty($socialNetworkId)) {
            return ["success" => false, "message" => "Social network ID missing"];
        }

        try {
            $request = "DELETE FROM social_network WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$socialNetworkId]);

            if ($pdo->rowCount() > 0) {
                return ["success" => true, "message" => "Social network deleted successfully"];
            } else {
                return ["success" => false, "message" => "Social network not found"];
            }

        } catch (\PDOException $e) {
            error_log("Error when deleting social network: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

<?php

namespace AdminSocialNetworks;

use App\Database;

class AdminUpdateSocialNetwork
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getSocialNetworkById()
    {

        $socialNetworkId = $_GET['socialNetworkId'] ?? null;

        if (empty($socialNetworkId)) {
            return ["success" => false, "message" => "Social network ID missing"];
        }

        try {
            $request = "SELECT * FROM social_network WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$socialNetworkId]);
            $socialNetwork = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($socialNetwork) {
                return ["success" => true, "socialNetwork" => $socialNetwork];
            } else {
                return ["success" => false, "message" => "Social network not found"];
            }
        } catch (\PDOException $e) {
            error_log("Error when fetching social network: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    public function updateSocialNetwork()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $socialNetworkId = isset($data['id']) ? trim(strip_tags($data['id'])) : null;
        $platform = isset($data['platform']) ? trim(strip_tags($data['platform'])) : null;
        $url = isset($data['url']) ? trim(strip_tags($data['url'])) : null;

        if (empty($socialNetworkId) || empty($platform) || empty($url)) {
            return ["success" => false, "message" => "All fields must be filled"];
        }

               // Récupérer les données actuelles du produit
        $request = "SELECT * FROM social_network WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$socialNetworkId]);
        $existingSocialNetwork = $pdo->fetch(\PDO::FETCH_ASSOC);

           // Vérifier si aucune modification n'a été apportée
        if(
            $platform ==  $existingSocialNetwork ['platform'] &&
            $url == $existingSocialNetwork ['url']
        ) {
            return ["success" => false, "message" => "No changes detected"];
        }


        try {
            $request = "UPDATE social_network SET platform = ?, url = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$platform, $url, $socialNetworkId]);

            return ["success" => true, "message" => "Social network updated successfully!!", "socialNetwork" => [
                'id' => $socialNetworkId,
                'platform' => $platform,
                'url' => $url,
            ]];
        } catch (\PDOException $e) {
            error_log("Error when updating social network: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

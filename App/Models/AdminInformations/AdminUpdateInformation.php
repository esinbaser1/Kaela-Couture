<?php

namespace AdminInformations;

use App\Database;

class AdminUpdateInformation
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getInformationById()
    {
        $informationId = isset($_GET['informationId']) ? $_GET['informationId'] : null; // vérifier si un informationId est passé dans la requête lors de la récupération des informations 

        if (empty($informationId)) {
            return ["success" => false, "message" => "Information ID missing"];
        }

        try {
            $request = "SELECT * FROM about_me WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$informationId]);
            $information = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($information) {
                return ["success" => true, "information" => $information];
            } else {
                return ["success" => false, "message" => "Information not found"];
            }
        } catch (\PDOException $e) {
            error_log("Error when fetching information: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }

    public function updateInformation()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $informationId = isset($data['id']) ? strip_tags($data['id']) : null;
        $description = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $mobile = isset($data['mobile']) ? trim(strip_tags($data['mobile'])) : null;
        $address = isset($data['address']) ? trim(strip_tags($data['address'])) : null;

        if (empty($mobile) && empty($description) && empty($address)) {
            return ["success" => false, "message" => "At least one field must be filled"];
        }

        // Récupérer les données actuelles du produit
        $request = "SELECT * FROM about_me WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$informationId]);
        $existingInformation = $pdo->fetch(\PDO::FETCH_ASSOC);

        // Vérifier si aucune modification n'a été apportée
        if (
            $description == $existingInformation['description'] &&
            $mobile == $existingInformation['mobile'] &&
            $address == $existingInformation['address']
        ) 
        {
            return ["success" => false, "message" => "No changes detected"];
        }

        try {
            $request = "UPDATE about_me SET description = ?, mobile = ?, address = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$description, $mobile, $address, $informationId]);

            return ["success" => true, "message" => "Information updated successfully", "information" => [
                'id' => $informationId,
                'description' => $description,
                'mobile' => $mobile,
                'address' => $address,
            ]];
        } catch (\PDOException $e) {
            error_log("Error when updating information: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}

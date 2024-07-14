<?php
namespace AdminInformations;

use App\Database;

class AdminDeleteInformation
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function deleteInformation()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $informationId = isset($data['informationId']) ? strip_tags($data['informationId']) : null;

        if (empty($informationId)) 
        {
            return ["success" => false, "message" => "Information ID missing"];
        }

        try {
            $request = "DELETE FROM about_me WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$informationId]);

            if ($pdo->rowCount() > 0) 
            {
                return ["success" => true, "message" => "Information deleted successfully"];
            } 
            else 
            {
                return ["success" => false, "message" => "Information not found"];
            }
        }
         catch (\PDOException $e) 
        {
            error_log("Error when deleting information: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}


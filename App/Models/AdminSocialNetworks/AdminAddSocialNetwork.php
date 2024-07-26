<?php

namespace AdminSocialNetworks;

use App\Database;

class AdminAddSocialNetwork
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addSocialNetwork()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $platform = isset($data['platform']) ? strip_tags($data['platform']) : null;
        $url = isset($data['url']) ? strip_tags($data['url']) : null;

        try 
        {
            $request = "INSERT INTO social_network (platform, url) VALUES (?, ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$platform, $url]);

            $id = $this->db->lastInsertId();
            
            $newSocialNetwork = [
                'id' => $id,
                'platform' => $platform,
                'url' => $url,
            ];

            return ["success" => true, "message" => "Social network added successfully!!!", "socialNetwork" => $newSocialNetwork];

        } 
        catch (\PDOException $e) 
        {
            error_log("Error when creating social network: " . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
    }
}




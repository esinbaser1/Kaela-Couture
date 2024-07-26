<?php

namespace AdminSocialNetworks;
use App\Database;

class AdminSocialNetwork
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getSocialNetwork()
    {
        try 
        {

            $request = "SELECT * FROM social_network";
            $pdo = $this->db->query($request);
            $socialNetwork = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            
            return ["success" => true, "socialNetwork" => $socialNetwork];
        }
        catch(\PDOException $e)
        {
            error_log("Error when retrieving social networks :" . $e->getMessage());
            return ["success" => false, "message" => "Database error"];
        }
        {

        }
    }
}
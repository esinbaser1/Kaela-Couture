<?php


namespace Models;

use App\Database;
use PDOException;

class Logout
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function logoutUser($token)
    {
        try {
            $query = $this->db->prepare("DELETE FROM sessions WHERE token = ?");
            $query->execute([$token]);
            return ["success" => true, "message" => "Logout successful"];
        } catch (PDOException $e) {
            error_log("Error when logging out user: " . $e->getMessage());
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        }
    }
}

<?php

namespace Models;

use App\Database;

class AdminAddInformation
{
    protected $db;
    protected $slug;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addInformation()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $description = isset($data['description']) ? strip_tags($data['description']) : null;
        $mobile = isset($data['mobile']) ? strip_tags($data['mobile']) : null;
        $address = isset($data['address']) ? strip_tags($data['address']) : null;

        if (!preg_match('/^\+?[0-9]*$/', $mobile)) {
            return ["success" => false, "message" => "Invalid mobile number format"];
        }

        try
        {
            $request = "INSERT INTO about_me (description, mobile, address) VALUES (?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$description, $mobile, $address]);

            return ["success" => true, "message" => "Information added successfully"];

        }

        catch (\PDOException $e) 
        {
            error_log("Error when creating information: " . $e->getMessage());

            return ["success" => false, "message" => "Database error"];
        }
        
    }



}
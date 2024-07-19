<?php

namespace AdminCategories;

use App\Database;

class AdminUpdateCategory
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }


public function updateCategory()
{
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    $informationId = isset($data['id']) ? strip_tags($data['id']) : null;
    $description = isset($data['description']) ? strip_tags($data['description']) : null;
    $mobile = isset($data['mobile']) ? strip_tags($data['mobile']) : null;
    $address = isset($data['address']) ? strip_tags($data['address']) : null;

    if (empty($informationId) || empty($description) || empty($mobile) || empty($address)) 
    {
        error_log("Missing information for update");
        return ["success" => false, "message" => "Missing information for update"];
    }

    try 
    {
        $request = "UPDATE about_me SET description = ?, mobile = ?, address = ? WHERE id = ?";
        $pdo = $this->db->prepare($request);
        $pdo->execute([$description, $mobile, $address, $informationId]);

        return ["success" => true, "message" => "Information updated successfully", "information" => [
            'id' => $informationId,
            'description' => $description,
            'mobile' => $mobile,
            'address' => $address,
        ]];
    } catch (\PDOException $e) 
    {
        error_log("Error when updating information: " . $e->getMessage());
        return ["success" => false, "message" => "Database error"];
    }
}

}

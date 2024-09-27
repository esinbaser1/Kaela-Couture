<?php

namespace AdminInformations;

use App\Database;

// Class to handle adding new information in the admin panel
class AdminAddInformation
{
    protected $db; 

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection();
    }

    // Method to add new information 
    public function addInformation()
    {
        // Retrieve the input data from the HTTP request and decode the JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Clean the incoming data, using trim to remove extra spaces and strip_tags for sanitization
        $description = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $mobile = isset($data['mobile']) ? trim(strip_tags($data['mobile'])) : null;
        $address = isset($data['address']) ? trim(strip_tags($data['address'])) : null;

        // Check if at least one field is filled, return an error if all are empty
        if (empty($description) && empty($mobile) && empty($address)) {
            return ["success" => false, "message" => "At least one field must be filled"];
        }

        // Validate the mobile number format if provided, allowing numbers and an optional "+" sign
        if (!empty($mobile) && !preg_match('/^\+?[0-9]*$/', $mobile)) 
        {
            return ["success" => false, "message" => "Invalid mobile number format"];
        }

        try
        {
            // SQL query to insert the new information
            $request = "INSERT INTO information (description, mobile, address) VALUES (?, ?, ?)";
            $pdo = $this->db->prepare($request); 
            $pdo->execute([$description, $mobile, $address]);

            // Get the ID of the newly inserted information
            $id = $this->db->lastInsertId();
            
            // Prepare the new information data to return in the response
            $newInformation = [
                'id' => $id,
                'description' => $description,
                'mobile' => $mobile,
                'address' => $address
            ];

            // Return a success response with the new information
            return ["success" => true, "message" => "Information added successfully!!!", "information" => $newInformation];

        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

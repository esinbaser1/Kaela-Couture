<?php

namespace AdminInformations;

use App\Database;

// Class to handle updating existing information in the admin panel
class AdminUpdateInformation
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection();
    }

    // Private method to fetch information by its ID
    private function fetchInformationById($informationId)
    {
        // SQL query to select the information from the 'information' table by ID
        $request = "SELECT * FROM information WHERE id = ?";
        $pdo = $this->db->prepare($request); 
        $pdo->execute([$informationId]); 
        return $pdo->fetch(\PDO::FETCH_ASSOC); 
    }

    // Method to retrieve information by its ID
    public function getInformationById()
    {
        // Get the information ID from the GET request
        $informationId = isset($_GET['informationId']) ? $_GET['informationId'] : null;

        // If the information ID is missing, return an error message
        if (empty($informationId)) 
        {
            return ["success" => false, "message" => "Information ID missing"];
        }

        // Use the private method to fetch the information by ID
        $information = $this->fetchInformationById($informationId);

        // If information is found, return a success response with the data
        if ($information) 
        {
            return ["success" => true, "information" => $information];
        } 
        else 
        {
            // If no information is found, return an error response
            return ["success" => false, "message" => "Information not found"];
        }
    }

    // Method to update existing information
    public function updateInformation()
    {
        // Get the input data from the HTTP request and decode the JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the input fields
        $informationId = isset($data['id']) ? strip_tags($data['id']) : null;
        $description = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $mobile = isset($data['mobile']) ? trim(strip_tags($data['mobile'])) : null;
        $address = isset($data['address']) ? trim(strip_tags($data['address'])) : null;

        // Ensure that at least one field is filled, return an error if all fields are empty
        if (empty($mobile) && empty($description) && empty($address)) 
        {
            return ["success" => false, "message" => "At least one field must be filled"];
        }

        // Validate the mobile number format if provided, allowing numbers and an optional "+" sign
        if (!empty($mobile) && !preg_match('/^\+?[0-9]*$/', $mobile)) 
        {
            return ["success" => false, "message" => "Invalid mobile number format"];
        }

        // Fetch the existing information from the database using the private method
        $existingInformation = $this->fetchInformationById($informationId);

        // Check if any changes were made compared to the current data
        if (
            $description == $existingInformation['description'] &&
            $mobile == $existingInformation['mobile'] &&
            $address == $existingInformation['address']
        ) 
        {
            return ["success" => false, "message" => "No changes detected"];
        }

        try 
        {
            // SQL query to update the information in the database
            $request = "UPDATE information SET description = ?, mobile = ?, address = ? WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$description, $mobile, $address, $informationId]);

            // Return a success response with the updated information
            return ["success" => true, "message" => "Information updated successfully", "information" => [
                'id' => $informationId,
                'description' => $description,
                'mobile' => $mobile,
                'address' => $address,
            ]];
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "Database error"];
        }
    }
}

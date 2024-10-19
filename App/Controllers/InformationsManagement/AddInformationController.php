<?php

namespace Controllers\InformationsManagement;

use Models\InformationsManagement\AddInformationModel;

class AddInformationController
{
    protected $model;

    public function __construct()
    {
        $this->model = new AddInformationModel();
    }

    // Method to handle the addition of information
    public function addInformation()
    {
        // Retrieve data from the HTTP request
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Clean input data
        $description = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $mobile = isset($data['mobile']) ? trim(strip_tags($data['mobile'])) : null;
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $address = isset($data['address']) ? trim(strip_tags($data['address'])) : null;

        // Check if at least one field is filled
        if (empty($description) && empty($mobile) && empty($email) && empty($address)) 
        {
            return ["success" => false, "message" => "At least one field must be filled"];
        }

        // Validate the email format only if email is not empty
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            return ["success" => false, "message" => "Invalid email"];
        }

        // Validate the phone number format
        if (!empty($mobile) && !preg_match('/^\+?[0-9]*$/', $mobile)) 
        {
            return ["success" => false, "message" => "Invalid mobile number format"];
        }

        try 
        {
            // Insert the information via the model
            $id = $this->model->insertInformation($description, $mobile, $email, $address);

            // Prepare the data to be returned
            $newInformation = [
                'id' => $id,
                'description' => $description,
                'mobile' => $mobile,
                'email' => $email,
                'address' => $address
            ];

            // Return a success response
            return ["success" => true, "message" => "Information added successfully!!!", "information" => $newInformation];
        } 
        catch (\Exception $e) 
        {
            // Handle errors and return a failure response
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
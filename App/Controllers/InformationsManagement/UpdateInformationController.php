<?php

namespace Controllers\InformationsManagement;

use Models\InformationsManagement\UpdateInformationModel;

class UpdateInformationController 
{
    protected $model;

    // Initializes the UpdateInformationModel
    public function __construct()
    {
        $this->model = new UpdateInformationModel();
    }

    // Method to get information by its ID
    public function getInformationById()
    {
        $informationId = isset($_GET['informationId']) ? $_GET['informationId'] : null;

        if (empty($informationId)) 
        {
            return ["success" => false, "message" => "Information ID missing"];
        }

        // Fetch the information by its ID using the model
        $information = $this->model->getInformationById($informationId);

        if ($information) 
        {
            // If data is available, return it
            return ["success" => true, "information" => $information];
        } 
        else 
        {
            return ["success" => false, "message" => "Information not found"];
        }
    }

    // Method to handle updating information
    public function updateInformation()
    {
        // Retrieve the input data from the HTTP request and decode it from JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the informations details from the input data
        $informationId = isset($data['id']) ? strip_tags($data['id']) : null;
        $description = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $mobile = isset($data['mobile']) ? trim(strip_tags($data['mobile'])) : null;
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $address = isset($data['address']) ? trim(strip_tags($data['address'])) : null;

        // Check if any required fields are missing
        if (empty($mobile) && empty($description) && empty($email) && empty($address)) 
        {
            return ["success" => false, "message" => "At least one field must be filled"];
        }

       
        // Validate the email format only if email is not empty
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            return ["success" => false, "message" => "Invalid email"];
        }


        // Check that the telephone number is in the correct format
        if (!empty($mobile) && !preg_match('/^\+?[0-9]*$/', $mobile)) 
        {
            return ["success" => false, "message" => "Invalid mobile number format"];
        }

        try 
        {
            $existingInformation = $this->model->getInformationById($informationId);

            if ($description == $existingInformation['description'] && $mobile == $existingInformation['mobile'] && $email == $existingInformation['email'] && $address == $existingInformation['address']) 
            {
                return ["success" => false, "message" => "No changes detected"];
            }

            // Update the information using the model
            $updatedRows = $this->model->updateInformation($informationId, $description, $mobile, $email, $address);

            if ($updatedRows > 0) 
            {
                return ["success" => true, "message" => "Information updated successfully"];
            } 
            else 
            {
                return ["success" => false, "message" => "Update failed or no changes detected"];
            }
        } 
        catch (\Exception $e) 
        {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
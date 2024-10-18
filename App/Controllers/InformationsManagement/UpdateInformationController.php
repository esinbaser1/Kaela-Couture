<?php

namespace Controllers\InformationsManagement;

use Models\InformationsManagement\UpdateInformationModel;

class UpdateInformationController 
{
    protected $updateInformationModel;

    // Initializes the UpdateInformationModel
    public function __construct()
    {
        $this->updateInformationModel = new UpdateInformationModel();
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
        $information = $this->updateInformationModel->getInformationById($informationId);

        if ($information) 
        {
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
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $informationId = isset($data['id']) ? strip_tags($data['id']) : null;
        $description = isset($data['description']) ? trim(strip_tags($data['description'])) : null;
        $mobile = isset($data['mobile']) ? trim(strip_tags($data['mobile'])) : null;
        $address = isset($data['address']) ? trim(strip_tags($data['address'])) : null;

        if (empty($mobile) && empty($description) && empty($address)) 
        {
            return ["success" => false, "message" => "At least one field must be filled"];
        }

        if (!empty($mobile) && !preg_match('/^\+?[0-9]*$/', $mobile)) 
        {
            return ["success" => false, "message" => "Invalid mobile number format"];
        }

        try 
        {
            $existingInformation = $this->updateInformationModel->getInformationById($informationId);

            if ($description == $existingInformation['description'] && $mobile == $existingInformation['mobile'] && $address == $existingInformation['address']) 
            {
                return ["success" => false, "message" => "No changes detected"];
            }

            // Update the information using the model
            $updatedRows = $this->updateInformationModel->updateInformation($informationId, $description, $mobile, $address);

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
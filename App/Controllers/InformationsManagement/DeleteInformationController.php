<?php

namespace Controllers\InformationsManagement;

use Models\InformationsManagement\DeleteInformationModel;

class DeleteInformationController 
{
    protected $model;

    // Initializes the model
    public function __construct()
    {
        $this->model = new DeleteInformationModel();
    }

    // Method to handle the deletion of information
    public function deleteInformation()
    {
        // Retrieves data from the HTTP request
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitizes and checks if the information ID is provided
        $informationId = isset($data['informationId']) ? strip_tags($data['informationId']) : null;
        if (empty($informationId)) 
        {
            return ["success" => false, "message" => "Information ID missing"];
        }

        try 
        {
            // Calls the model to delete the information by its ID
            $deleted = $this->model->deleteInformationById($informationId);

            // Returns a response based on the success or failure of the deletion
            if ($deleted) 
            {
                return ["success" => true, "message" => "Information deleted successfully"];
            } 
            else 
            {
                return ["success" => false, "message" => "Information not found"];
            }
        } 
        catch (\Exception $e) 
        {
            // Handles errors and returns a failure response
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
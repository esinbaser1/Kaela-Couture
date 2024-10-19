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
        // Sanitize and get the social network ID from the HTTP request
        $informationId = isset($_GET['informationId']) ? strip_tags($_GET['informationId']) : null;

        // Check if the ID is missing
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
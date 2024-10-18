<?php

namespace Controllers\InformationsManagement;

use Models\InformationsManagement\InformationModel;

class InformationController
{
    protected $model;

    // Initializes the Information model
    public function __construct()
    {
        $this->model = new InformationModel();
    }

    // Method to get information data
    public function getInformations()
    {
        try 
        {
            // Fetch the informations from the model
            $information = $this->model->getInformations();

            // Return the list of informations with a success response
            return ["success" => true, "information" => $information];
        }
        catch (\Exception $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
<?php

namespace Controllers\SocialNetworksManagement;

use Models\SocialNetworksManagement\DeleteSocialNetworkModel;

class DeleteSocialNetworkController
{
    protected $model;

    public function __construct()
    {
        $this->model = new DeleteSocialNetworkModel();
    }

    // Method to handle the deletion of a social network
    public function deleteSocialNetwork()
    {
        // Sanitize and get the social network ID from the HTTP request
        $socialNetworkId = isset($_GET['socialNetworkId']) ? strip_tags($_GET['socialNetworkId']) : null;

        // Check if the ID is missing
        if (empty($socialNetworkId)) 
        {
            return ["success" => false, "message" => "Social network ID is missing"];
        }

        // Use the model to delete the social network
        $rowCount = $this->model->deleteSocialNetwork($socialNetworkId);

        // Prepare the response based on the result from the model
        if ($rowCount > 0) 
        {
            return ["success" => true, "message" => "Social network deleted successfully"];
        } 
        else 
        {
            return ["success" => false, "message" => "Social network not found"];
        }
    }
}
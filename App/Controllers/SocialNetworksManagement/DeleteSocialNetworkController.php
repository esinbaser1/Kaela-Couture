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
        // Retrieve the input data from the HTTP request (JSON format)
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and get the social network ID from the input data
        $socialNetworkId = isset($data['socialNetworkId']) ? strip_tags($data['socialNetworkId']) : null;

        // Validate the input
        if (empty($socialNetworkId)) {
            return ["success" => false, "message" => "Social network ID is missing"];
        }

        // Use the model to delete the social network
        $rowCount = $this->model->deleteSocialNetwork($socialNetworkId);

        // Prepare the response based on the result from the model
        if ($rowCount > 0) {
            return ["success" => true, "message" => "Social network deleted successfully"];
        } else {
            return ["success" => false, "message" => "Social network not found"];
        }
    }
}
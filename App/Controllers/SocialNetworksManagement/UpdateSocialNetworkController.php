<?php

namespace Controllers\SocialNetworksManagement;

use Models\SocialNetworksManagement\UpdateSocialNetworkModel;

class UpdateSocialNetworkController
{
    protected $model;

    public function __construct()
    {
        $this->model = new UpdateSocialNetworkModel();
    }

    // Method to retrieve a social network by ID
    public function getSocialNetworkById()
    {
        $socialNetworkId = isset($_GET['socialNetworkId']) ? strip_tags($_GET['socialNetworkId']) : null;

        // Validate input
        if (empty($socialNetworkId)) {
            return ["success" => false, "message" => "Social network ID is missing"];
        }

        $socialNetwork = $this->model->getSocialNetworkById($socialNetworkId);

        if ($socialNetwork) {
            return ["success" => true, "socialNetwork" => $socialNetwork];
        } else {
            return ["success" => false, "message" => "Social network not found"];
        }
    }

    // Method to handle the update of a social network
    public function updateSocialNetwork()
    {
        // Get the input data from the request body (JSON format)
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $socialNetworkId = isset($data['id']) ? trim(strip_tags($data['id'])) : null;
        $platform = isset($data['platform']) ? trim(strip_tags($data['platform'])) : null;
        $url = isset($data['url']) ? trim(strip_tags($data['url'])) : null;

        // Validate input
        if (empty($socialNetworkId) || empty($platform) || empty($url)) {
            return ["success" => false, "message" => "All fields must be filled"];
        }

        // Check if the URL is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ["success" => false, "message" => "Invalid URL"];
        }

        // Fetch the existing social network data
        $existingSocialNetwork = $this->model->getSocialNetworkById($socialNetworkId);

        // Check if any changes were made
        if ($existingSocialNetwork['platform'] === $platform && $existingSocialNetwork['url'] === $url) {
            return ["success" => false, "message" => "No changes detected"];
        }

        // Update the social network using the model
        $rowCount = $this->model->updateSocialNetwork($socialNetworkId, $platform, $url);

        if ($rowCount > 0) {
            return [
                "success" => true,
                "message" => "Social network updated successfully",
                "socialNetwork" => [
                    'id' => $socialNetworkId,
                    'platform' => $platform,
                    'url' => $url,
                ]
            ];
        } else {
            return ["success" => false, "message" => "No updates made"];
        }
    }
}
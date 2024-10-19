<?php

namespace Controllers\SocialNetworksManagement;

use Models\SocialNetworksManagement\AddSocialNetworkModel;

class AddSocialNetworkController
{
    protected $model;

    public function __construct()
    {
        $this->model = new AddSocialNetworkModel();
    }

    // Method to handle adding a new social network
    public function addSocialNetwork()
    {
        // Get the input data from the request body (JSON format)
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and retrieve the platform and URL from the input
        $platform = isset($data['platform']) ? trim(strip_tags($data['platform'])) : null;
        $url = isset($data['url']) ? trim(strip_tags($data['url'])) : null;

        // Validate input
        if (empty($platform) || empty($url)) 
        {
            return ["success" => false, "message" => "Please complete all fields"];
        }

        // Check if the URL is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) 
        {
            return ["success" => false, "message" => "Invalid URL"];
        }

        // Uses the model function to check whether the platform and url are already in use
        if ($this->model->existsInColumn('platform', $platform)) 
        {
            return ["success" => false, "message" => "This social network name is already in use."];
        }

        if ($this->model->existsInColumn('url', $url)) 
        {
            return ["success" => false, "message" => "This url is already in use."];
        }


        try 
        {
            // Use the model to insert the new social network
            $id = $this->model->insertSocialNetwork($platform, $url);

            // Prepare the newly added social network data for the response
            return [
                "success" => true,
                "message" => "Social network added successfully!",
                "socialNetwork" => [
                    'id' => $id,
                    'platform' => $platform,
                    'url' => $url
                ]
            ];
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response in case of a database error
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }
}
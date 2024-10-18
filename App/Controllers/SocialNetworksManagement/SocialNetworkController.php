<?php

namespace Controllers\SocialNetworksManagement;

use Models\SocialNetworksManagement\SocialNetworkModel;

class SocialNetworkController
{
    protected $model;

    public function __construct()
    {
        $this->model = new SocialNetworkModel();
    }

    // Method to handle the retrieval of all social networks
    public function getSocialNetworks()
    {
        // Use the model to retrieve the social networks
        return $this->model->getSocialNetwork();
    }
}
<?php

use Controllers\SocialNetworksManagement\AddSocialNetworkController;
use Controllers\SocialNetworksManagement\DeleteSocialNetworkController;
use Controllers\SocialNetworksManagement\SocialNetworkController;
use Controllers\SocialNetworksManagement\UpdateSocialNetworkController;

function socialNetworkRoutes($adminAction, $authMiddleware) {

    $getSocialNetwork = new SocialNetworkController();
    $addSocialNetwork = new AddSocialNetworkController();
    $updateSocialNetwork = new UpdateSocialNetworkController();
    $deleteSocialNetwork = new DeleteSocialNetworkController();
    
    switch ($adminAction) 
    {
        case "getSocialNetwork":
            return $getSocialNetwork->getSocialNetworks();

        case "getSocialNetworkById":
            return $updateSocialNetwork->getSocialNetworkById();

        case "addSocialNetwork":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $addSocialNetwork->addSocialNetwork();
            }

        case "updateSocialNetwork":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $updateSocialNetwork->updateSocialNetwork();
            }

        case "deleteSocialNetwork":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $deleteSocialNetwork->deleteSocialNetwork();
            }

        default:
            return null; 
    }
}

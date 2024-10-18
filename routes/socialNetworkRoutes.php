<?php

use Controllers\SocialNetworksManagement\AddSocialNetworkController;
use Controllers\SocialNetworksManagement\DeleteSocialNetworkController;
use Controllers\SocialNetworksManagement\SocialNetworkController;
use Controllers\SocialNetworksManagement\UpdateSocialNetworkController;

function handleSocialNetworkRoutes($adminAction, $authMiddleware) {
    
    switch ($adminAction) {
        case "getSocialNetwork":
            $getSocialNetwork = new SocialNetworkController();
            return $getSocialNetwork->getSocialNetworks();

        case "getSocialNetworkById":
            $getSocialNetworkById = new UpdateSocialNetworkController();
            return $getSocialNetworkById->getSocialNetworkById();

        case "addSocialNetwork":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $addSocialNetwork = new AddSocialNetworkController();
                return $addSocialNetwork->addSocialNetwork();
            }

        case "updateSocialNetwork":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $updateSocialNetwork = new UpdateSocialNetworkController();
                return $updateSocialNetwork->updateSocialNetwork();
            }

        case "deleteSocialNetwork":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $deleteSocialNetwork = new DeleteSocialNetworkController();
                return $deleteSocialNetwork->deleteSocialNetwork();
            }

        default:
            return null; 
    }
}

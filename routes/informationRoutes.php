<?php

use Controllers\InformationsManagement\AddInformationController;
use Controllers\InformationsManagement\DeleteInformationController;
use Controllers\InformationsManagement\InformationController;
use Controllers\InformationsManagement\UpdateInformationController;

function informationRoutes($adminAction, $authMiddleware) {

    $getInformation = new InformationController();
    $addInformation = new AddInformationController();
    $updateInformation = new UpdateInformationController();
    $deleteInformation = new DeleteInformationController();
    
    switch ($adminAction) 
    {
        case "getInformation":
            return $getInformation->getInformations();

        case "getInformationById":
            return $updateInformation->getInformationById();

        case "addInformation":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $addInformation->addInformation();
            }

        case "updateInformation":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $updateInformation->updateInformation();
            }

        case "deleteInformation":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) 
            {
                return $authResult;
            } 
            else 
            {
                return $deleteInformation->deleteInformation();
            }

        default:
            return null;
    }
}

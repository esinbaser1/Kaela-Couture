<?php

use Controllers\InformationsManagement\AddInformationController;
use Controllers\InformationsManagement\DeleteInformationController;
use Controllers\InformationsManagement\InformationController;
use Controllers\InformationsManagement\UpdateInformationController;

function handleInformationRoutes($adminAction, $authMiddleware) {
    
    switch ($adminAction) {
        case "getInformation":
            $getInformation = new InformationController();
            return $getInformation->getInformations();

        case "getInformationById":
            $getInformationById = new UpdateInformationController();
            return $getInformationById->getInformationById();

        case "addInformation":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $addInformation = new AddInformationController();
                return $addInformation->addInformation();
            }

        case "updateInformation":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $updateInformation = new UpdateInformationController();
                return $updateInformation->updateInformation();
            }

        case "deleteInformation":
            $authResult = $authMiddleware->verifyAccess('admin');
            if ($authResult !== null) {
                return $authResult;
            } else {
                $deleteInformation = new DeleteInformationController();
                return $deleteInformation->deleteInformation();
            }

        default:
            return null;
    }
}

<?php

use Controllers\SectionsManagement\SectionController;

function handleSectionRoutes($adminAction) {
    switch($adminAction) {
        case 'getSection' :
            $getSection = new SectionController();
            return $getSection->getSections();

            default:
            return null;
    }
}
<?php

use Controllers\SectionsManagement\SectionController;

function sectionRoutes($adminAction) 
{
    $getSection = new SectionController();

    switch($adminAction) 
    {
        case 'getSection' :
            return $getSection->getSections();

            default:
            return null;
    }
}
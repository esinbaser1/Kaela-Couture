<?php

namespace Controllers\SectionsManagement;

use Models\SectionsManagement\SectionModel;

class SectionController 
{
    protected $model;

    public function __construct()
    {
        $this->model = new SectionModel();
    }

    public function getSections()
    {
        return $this->model->getSection();
    }
}
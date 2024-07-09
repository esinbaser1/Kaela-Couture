<?php

namespace Models;

use App\Database;

class AdminInformationModify
{
    protected $db;
    protected $slug;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function updateInformation()
    {
        
    }



}
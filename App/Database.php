<?php

namespace App;

class Database 
{
    protected $cnx;
    protected $host = 'localhost';
    protected $db = 'kaela_couture';
    protected $login = 'root';
    protected $pw = 'root';
    
    public function __construct() 
    {
        $this->cnx = new \PDO("mysql:host=$this->host;dbname=$this->db", $this->login, $this->pw);
        $this->cnx->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    public function getConnection() 
    {
        return $this->cnx;
    }
}  





<?php
namespace Lib;

class Slug 
{

    public $string;
    public $slug;

    public function sluguer($string) 

    {
        $string = iconv("utf-8", "ASCII//TRANSLIT", $string);
        $slug = strtolower(trim(preg_replace("/[^A-Za-z0-9-]+/", "-", $string)));
        return $slug;
    }
} 
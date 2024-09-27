<?php
namespace Lib;

// The Slug class is responsible for generating a URL-friendly slug from a given string
class Slug 
{
    public $string;  // Property to hold the original string
    public $slug;    // Property to hold the generated slug

    // Method to create a slug from the provided string
    public function sluguer($string) 
    {
        // Convert the string from UTF-8 to ASCII, removing any special characters
        $string = iconv("utf-8", "ASCII//TRANSLIT", $string);

        // Replace any non-alphanumeric characters with a hyphen (-) and convert the string to lowercase
        $slug = strtolower(trim(preg_replace("/[^A-Za-z0-9-]+/", "-", $string)));

        // Return the cleaned, URL-friendly slug
        return $slug;
    }
}
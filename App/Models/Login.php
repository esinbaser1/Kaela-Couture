<?php

namespace Models;

use App\Database;

/* ********* Class Login Handles user login functionality ********* */

class Login
{
    protected $db;

    /* ********* Constructor initializes the database connection ********* */
    public function __construct()
    {
        // Create a new instance of the Database class and get the database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }

     /* *********  Method to authenticate a user based on provided email and password ********* */
    public function getUser()
    {
        // Get the raw POST data and decode it as JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Extract email and password from the decoded data
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;


        // Validate that email and password are provided and email is valid
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$password) 
        {
            return ["success" => false, "message" => "All fields are required"];
        }

        try 
        {
            // Prepare a SQL statement to select the user with the provided email
            $request = "SELECT * FROM user WHERE email = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email]);

            // Fetch the user data from the database
            $user = $pdo->fetch();

            // Verify the provided password with the hashed password in the database
            if ($user && password_verify($password, $user['password'])) 
            {
                return ["success" => true, "message" => "Login successful"];
            } 
            else 
            {
                return ["success" => false, "message" => "Incorect email or password"];
            }

        } 
        catch(\PDOException $e) 
        {
            // Log the error message and return a response indicating an error occurred
            error_log("Error when creating user: " . $e->getMessage());
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        }
    }
}
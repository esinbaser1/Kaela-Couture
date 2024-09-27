<?php

namespace Models;

use App\Database;
use Models\Token;

// This class handles user login functionality
class Login
{
    protected $db;
    protected $token;

    // Initializes the database connection and creates an instance of the Token class
    public function __construct()
    {
        $database = new Database(); 
        $this->db = $database->getConnection(); 
        $this->token = new Token();
    }

    // Method to authenticate a user using their email and password
    public function getUser()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and validate the email and password fields
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $password = isset($data['password']) ? strip_tags($data['password']) : null;

        // Check if email or password is missing
        if (empty($email) || empty($password)) 
        {
            return ["success" => false, "message" => "All fields are required."];
        }

        // Validate the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            return ["success" => false, "message" => "Invalid credentials."];
        }

        try {
            // Prepare an SQL query to search for the user by email
            $request = "SELECT * FROM user WHERE email = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email]);

            $user = $pdo->fetch(\PDO::FETCH_ASSOC);

            // Verify the password and check if the user exists
            if ($user && password_verify($password, $user['password'])) 
            {
                // If credentials are valid, generate a JWT token
                $token = $this->token->generateToken($user['id'], $user['role'], $user['username']);

                // Return a success response with the user details and JWT token
                return [
                    "success" => true,
                    "message" => "Login successful.",
                    "role" => $user['role'],
                    "user_id" => $user['id'],
                    "username" => $user['username'],
                    "token" => $token
                ];
            } 
            else 
            {
                // Return an error if the credentials are invalid
                return ["success" => false, "message" => "Invalid credentials."];
            }
        } 
        catch (\PDOException $e) 
        {
            // Return a failure response
            return ["success" => false, "message" => "An error occurred during login. Please try again later."];
        }
    }
}
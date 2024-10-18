<?php

namespace Controllers;

use Models\LoginModel;
use Utils\Token;

class LoginController
{
    protected $model;
    protected $token;

    public function __construct()
    {
        $this->model = new LoginModel();
        $this->token = new Token();
    }

    // Method to handle the user login process
    public function login()
    {
        // Retrieve the input data (JSON format)
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and validate the email and password fields
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $password = isset($data['password']) ? strip_tags($data['password']) : null;

        // Check if email or password is missing
        if (empty($email) || empty($password)) {
            return ["success" => false, "message" => "All fields are required."];
        }

        // Validate the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Invalid credentials."];
        }

        // Retrieve the user from the model
        $user = $this->model->getUserByEmail($email);

        // Verify the password and check if the user exists
        if ($user && password_verify($password, $user['password'])) {
            // If credentials are valid, generate a JWT token
            $token = $this->token->generateToken($user['id'], $user['role'], $user['username'], $user['email']);

            // Return a success response with the user details and JWT token
            return [
                "success" => true,
                "message" => "Login successful.",
                "role" => $user['role'],
                "user_id" => $user['id'],
                "username" => $user['username'],
                "email" => $user["email"],
                "token" => $token
            ];
        } else {
            // Return an error if the credentials are invalid
            return ["success" => false, "message" => "Invalid credentials."];
        }
    }
}
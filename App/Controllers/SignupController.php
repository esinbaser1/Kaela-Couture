<?php

namespace Controllers;

use Models\SignupModel;

class SignupController
{
    protected $model;

    public function __construct()
    {
        $this->model = new SignupModel();
    }

    // Method to handle user signup
    public function signup()
    {
        // Retrieve the input data (JSON format)
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and validate the user input
        $username = isset($data['username']) ? strip_tags($data['username']) : null;
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $password = isset($data['password']) ? strip_tags($data['password']) : null;

        // Check if any required field is missing
        if (empty($username) || empty($email) || empty($password)) {
            return ["success" => false, "message" => "All fields are required"];
        }

        // Validate the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Invalid email"];
        }

        // Check if the email is already registered
        if ($this->model->existsInColumn('email', $email)) 
        {
            return ["success" => false, "message" => "This email is already used."];
        }

        // Check if the username is already registered
        if ($this->model->existsInColumn('username', $username)) 
        {
            return ["success" => false, "message" => "This username is already used."];
        }

        // Validate the password strength
        if (!$this->validatePassword($password)) {
            return ["success" => false, "message" => "Password must contain at least one uppercase letter, one lowercase letter, one number, one special character, and be at least 8 characters long."];
        }

        // Hash the password before storing it in the database
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user into the database
        $userId = $this->model->addUser($username, $email, $passwordHash);

        // Prepare the user data to return in the response
        $userData = [
            'id' => $userId,
            "username" => $username,
            "email" => $email
        ];

        // Return a success response with the user data
        return ["success" => true, "message" => "Account created successfully. Redirecting...", "data" => $userData];
    }

    // Private method to validate the password strength
    private function validatePassword($password) 
    {
        // Password must have at least one uppercase letter, one lowercase letter, one digit, one special character, and be at least 8 characters long
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        return preg_match($pattern, $password);
    }
}
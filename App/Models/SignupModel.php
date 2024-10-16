<?php

namespace Models;

use App\Database;

// The Signup class handles user registration
class SignupModel 
{
    protected $db;

    // Initializes the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to create a new user account
    public function createUser()
    {
        // Retrieve the input data from the HTTP request (JSON format) and decode it
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Sanitize and validate the user input
        $username = isset($data['username']) ? strip_tags($data['username']) : null;
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $password = isset($data['password']) ? strip_tags($data['password']) : null;

        // Check if any required field is missing
        if (empty($username) || empty($email) || empty($password)) 
        {
            return ["success" => false, "message" => "All fields are required"];
        }

        // Check if the email is already registered
        if ($this->emailExists($email)) 
        {
            return ["success" => false, "message" => "This email is already used"];
        }

        // Validate the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            return ["success" => false, "message" => "Invalid email"];
        }

        // Check if the username is already registered
        if ($this->usernameExist($username)) 
        {
            return ["success" => false, "message" => "This username is already used"];
        }

        // Validate the password strength
        if (!$this->validatePassword($password)) 
        {
            return ["success" => false, "message" => "Password must contain at least one uppercase letter, one lowercase letter, one number, one special character, and be at least 8 characters long."];
        }

        // Hash the password before storing it in the database
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try 
        {
            // Insert the user data into the database, including the current timestamp for the last_active_at field
            $request = "INSERT INTO user (username, email, password, last_active_at) VALUES (?,?,?, NOW())";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$username, $email, $passwordHash]);

            // Get the ID of the newly inserted user
            $id = $this->db->lastInsertId();

            // Prepare the user data to return in the response
            $userData = [
                'id' => $id,
                "username" => $username,
                "email" => $email
            ];

            // Return a success response with the user data
            return ["success" => true, "message" => "Account created successfully. Redirecting...", "data" => $userData];

        } 
        catch (\PDOException $e) 
        {
            // Return an error response in case of a database issue
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        }
    }

    // Private method to check if an email already exists in the database
    private function emailExists($email) 
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $pdo->execute([$email]);
        return $pdo->fetchColumn() > 0;
    }

    // Private method to check if a username already exists in the database
    private function usernameExist($username) 
    {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
        $pdo->execute([$username]);
        return $pdo->fetchColumn() > 0;
    }

    // Private method to validate the password strength
    private function validatePassword($password) 
    {
        // Password must have at least one uppercase letter, one lowercase letter, one digit, one special character, and be at least 8 characters long
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        return preg_match($pattern, $password);
    }
}

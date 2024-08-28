<?php

namespace Models;

use App\Database;
/* ********* Class Login Signup user signup functionality ********* */

class Signup 
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function createUser()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $username = isset($data['username']) ? strip_tags($data['username']) : null;
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $password = isset($data['password']) ? strip_tags($data['password']) : null;

        if (empty($username) || empty($email) || empty($password)) 
        {
            return ["success" => false, "message" => "All fields are required"];
        }

        if ($this->emailExists($email)) {
            return ["success" => false, "message" => "This email is already used"];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Invalid email"];
        }

        if ($this->usernameExist($username)) {
            return ["success" => false, "message" => "This username is already used"];
        }

        if (!$this->validatePassword($password)) {
            return ["success" => false, "message" => "Password must contain at least one uppercase letter, one lowercase letter, one number, one special character, and be at least 8 characters long."];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try 
        {
            $request = "INSERT INTO user (username, email, password) VALUES (?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$username, $email, $passwordHash]);

            $id = $this->db->lastInsertId();

            $userData = [
                'id' => $id,
                "username" => $username,
                "email" => $email,
                "password" => $password
            ];

            return ["success" => true, "message" => "Account created successfully. Redirecting...", "data" => $userData];


        } 
        catch (\PDOException $e) 
        {
            error_log("Error when creating user: " . $e->getMessage());
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        }
    }

    private function emailExists($email) {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $pdo->execute([$email]);
        return $pdo->fetchColumn() > 0;
    }

    private function usernameExist($username) {
        $pdo = $this->db->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
        $pdo->execute([$username]);
        return $pdo->fetchColumn() > 0;
    }

    private function validatePassword($password) {
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        return preg_match($pattern, $password);
    }
}

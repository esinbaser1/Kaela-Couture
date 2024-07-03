<?php

namespace Models;

use App\Database;

class Signup 
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createUser()
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            return ["success" => false, "message" => "Method not allowed"];
        }
        
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if(!$username || !$email || !$password) 
        {
            return ["success" => false, "message" => "All fields are required"];
        }

        if ($this->emailExists($email)) {
            echo json_encode(["success" => false, "message" => "This email is already used"]);
            exit();
        }

        if ($this->usernameExists($username)) {
            echo json_encode(["success" => false, "message" => "This username is already used"]);
            exit();
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try 
        {
            $request = "INSERT INTO user (username, email, password) VALUES (?,?,?)";
            $pdo = $this->db->getConnection()->prepare($request);
            $pdo->execute([$username, $email, $passwordHash]);

            return ["success" => true, "message" => "User created successfully"];
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when creating user: " . $e->getMessage());
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        }
    }

    private function emailExists($email) {
        $pdo = $this->db->getConnection()->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $pdo->execute([$email]);
        return $pdo->fetchColumn() > 0;
    }
    private function usernameExists($username) {
        $pdo = $this->db->getConnection()->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
        $pdo->execute([$username]);
        return $pdo->fetchColumn() > 0;
    }
}

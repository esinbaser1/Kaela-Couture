<?php

namespace Models;

use App\Database;
use PDOException;

class Login
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getUser()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$password) {
            return ["success" => false, "message" => "All fields are required"];
        }

        try {
            $request = "SELECT * FROM user WHERE email = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email]);

            $user = $pdo->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $token = password_hash(uniqid(), PASSWORD_BCRYPT);
                $query = $this->db->prepare("INSERT INTO sessions (user_id, token) VALUES (?, ?)");
                $query->execute([$user['id'], $token]);
                return ["success" => true, "message" => "Login successful", "token" => $token];
            } else {
                return ["success" => false, "message" => "Incorrect email or password"];
            }

        } catch(PDOException $e) {
            error_log("Error when logging in user: " . $e->getMessage());
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        }
    }
}

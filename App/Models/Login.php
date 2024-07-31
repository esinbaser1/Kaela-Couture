<?php

namespace Models;

use App\Database;
use Models\Token;

class Login
{
    protected $db;
    protected $token;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->token = new Token();
    }

    public function getUser()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $password = isset($data['password']) ? strip_tags($data['password']) : null;

        if (empty($email) || empty($password)) {
            return ["success" => false, "message" => "All fields are required"];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Invalid email"];
        }

        try {
            $request = "SELECT * FROM user WHERE email = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email]);

            $user = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $token = $this->token->generateToken();
                $tokenExpireAt = $this->token->formatDate('+2 minutes');

                $request = "INSERT INTO sessions (user_id, token, expire_at) VALUES (?, ?, ?)";
                $pdo = $this->db->prepare($request);
                $pdo->execute([$user['id'], $token, $tokenExpireAt]);

                return [
                    "success" => true,
                    "message" => "Login successful",
                    "token" => $token,
                    "role" => $user['role'],
                    "user_id" => $user['id']
                ];
            } else {
                return ["success" => false, "message" => "Incorrect email or password"];
            }
        } catch (\PDOException $e) {
            error_log("Error when logging in user: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while processing your request"];
        }
    }
}
?>

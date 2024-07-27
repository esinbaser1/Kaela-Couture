<?php

namespace Models;

use App\Database;


class Login
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        date_default_timezone_set('Europe/Paris');
    }

    public function getUser()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null;
        $password = $data['password'] ?? null;

        if (empty($email) || empty($password)) 
        {
            http_response_code(400); // indique que la requête est mal formée ou invalide
            return ["success" => false, "message" => "All fields are required"];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            http_response_code(400);
            return ["success" => false, "message" => "Invalid email"];
        }


        try 
        {
            $request = "SELECT * FROM user WHERE email = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email]);

            $user = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) 
            {
                $token = $this->generateToken();

                $tokenExpireAt = date("Y-m-d H:i:s", strtotime("+7 days"));

                $request = "INSERT INTO sessions (user_id, token, expire_at) VALUES (?, ?, ?)";
                $pdo = $this->db->prepare($request);
                $pdo->execute([$user['id'], $token, $tokenExpireAt]);


                http_response_code(200);
                return [
                    "success" => true,
                    "message" => "Login successful",
                    "token" => $token,
                ];
            } 
            else 
            {
                http_response_code(401);
                return ["success" => false, "message" => "Incorrect email or password"];
            }
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when logging in user: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while processing your request"];
        }
    }

    public function verifyToken($token)
    {
        try {
            $request = "SELECT * FROM sessions WHERE token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$token]);

            $session = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($session) 
            {
                $currentTime = new \DateTime();
                $expireTime = new \DateTime($session['expire_at']);

                // Vérifie si le token expire dans moins de 1 jour
                if ($expireTime->diff($currentTime)->days < 1) {
                    // Renouvele le token
                    $this->renewToken($session['user_id'], $token);
                }

                return ["success" => true, "message" => "Token is valid"];
            } else 
            {
                http_response_code(401);
                return ["success" => false, "message" => "Token expired or invalid"];
            }
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when verifying token: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while verifying the token"];
        }
    }

    private function renewToken($userId, $oldToken)
    {
        try 
        {
            $newExpireAt = date("Y-m-d H:i:s", strtotime("+7 days"));
            $request = "UPDATE sessions SET expire_at = ? WHERE user_id = ? AND token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$newExpireAt, $userId, $oldToken]);
        } 
        catch (\PDOException $e) 
        {
            error_log("Error when renewing token: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while renewing the token"];
        }
    }

    private function generateToken()
    {
        return bin2hex(random_bytes(32));
    }
}

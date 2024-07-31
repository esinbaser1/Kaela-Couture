<?php

namespace Models;

use App\Database;
use DateTime;
use DateTimeZone;
use IntlDateFormatter;

class Login
{
    protected $db;

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Fonction pour récupérer un utilisateur et vérifier les identifiants
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
                $token = $this->generateToken();
                $tokenExpireAt = $this->formatDate('+2 minutes');
    
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
    

    // Fonction pour vérifier le token
    public function verifyToken($token)
    {
        try {
            $request = "SELECT * FROM sessions WHERE token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$token]);

            $session = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($session) {
                $currentTime = new DateTime();
                $expireTime = new DateTime($session['expire_at']);
                $newToken = $token;
                $updatedToken = false;

                // Ajout de journaux pour déboguer
                error_log("Current Time: " . $currentTime->format('Y-m-d H:i:s'));
                error_log("Expire Time: " . $expireTime->format('Y-m-d H:i:s'));

                // Renouvellement du token si l'expiration est dans moins de 1 minute
                if ($expireTime <= $currentTime) {
                    error_log("Token expired, renewing...");
                    $newToken = $this->renewToken($session['user_id'], $token);
                    $updatedToken = true;
                } else {
                    $interval = $currentTime->diff($expireTime);
                    if ($interval->i < 1) {
                        error_log("Token close to expiration, renewing...");
                        $newToken = $this->renewToken($session['user_id'], $token);
                        $updatedToken = true;
                    }
                }

                return [
                    "success" => true,
                    "message" => "Token is valid",
                    "token" => $newToken,
                    "updatedToken" => $updatedToken
                ];

            } else {
                error_log("Token expired or invalid");
                return ["success" => false, "message" => "Token expired or invalid"];
            }
        } catch (\PDOException $e) {
            error_log("Error when verifying token: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while verifying the token"];
        }
    }

    // Fonction pour renouveler le token
    private function renewToken($userId, $oldToken)
    {
        try {
            $newToken = $this->generateToken(); // Génération du nouveau token
            $newExpireAt = $this->formatDate('+2 minutes'); // Génération de la nouvelle date d'expiration

            // Mise à jour du token et de la date d'expiration dans la table des sessions
            $request = "UPDATE sessions SET token = ?, expire_at = ? WHERE user_id = ? AND token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$newToken, $newExpireAt, $userId, $oldToken]);

            error_log("Token renewed successfully for user_id: $userId");
            return $newToken;
        } catch (\PDOException $e) {
            error_log("Error when renewing token: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while renewing the token"];
        }
    }

    // Fonction pour générer un token aléatoire
    private function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    // Fonction pour formater une date
    private function formatDate($interval, $format = 'yyyy-MM-dd HH:mm:ss', $timezone = 'Europe/Paris', $locale = 'fr_FR')
    {
        $date = new DateTime();
        $date->modify($interval);
        $date->setTimezone(new DateTimeZone($timezone));

        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            $timezone,
            IntlDateFormatter::GREGORIAN,
            $format
        );

        return $formatter->format($date);
    }
}

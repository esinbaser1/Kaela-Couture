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
        $input = file_get_contents("php://input"); // Récupération des données d'entrée
        $data = json_decode($input, true); // Décodage des données JSON

        $email = isset($data['email']) ? filter_var($data['email'], FILTER_SANITIZE_EMAIL) : null; // Sanitize email
        $password = isset($data['password']) ? strip_tags($data['password']) : null; // Supprime les balises HTML

        // Vérification que les champs ne sont pas vides
        if (empty($email) || empty($password)) {
            return ["success" => false, "message" => "All fields are required"];
        }

        // Vérification de la validité de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Invalid email"];
        }

        try {
            // Préparation et exécution de la requête pour vérifier l'utilisateur
            $request = "SELECT * FROM user WHERE email = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email]);

            $user = $pdo->fetch(\PDO::FETCH_ASSOC);

            // Vérification du mot de passe
            if ($user && password_verify($password, $user['password'])) {
                
                $token = $this->generateToken(); // Génération du token

                $tokenExpireAt = $this->formatDate('+30 days'); // Génération de la date d'expiration du token

                // Insertion du token et de la date d'expiration dans la table des sessions
                $request = "INSERT INTO sessions (user_id, token, expire_at) VALUES (?, ?, ?)";
                $pdo = $this->db->prepare($request);
                $pdo->execute([$user['id'], $token, $tokenExpireAt]);

                return [
                    "success" => true,
                    "message" => "Login successful",
                    "token" => $token,
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
            // Préparation et exécution de la requête pour vérifier le token
            $request = "SELECT * FROM sessions WHERE token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$token]);

            $session = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($session) {
                $currentTime = new DateTime();
                $expireTime = new DateTime($session['expire_at']);
                $newToken = $token;
                $updatedToken = false;

                // Renouvellement du token si nécessaire
                if ($expireTime->diff($currentTime)->days < 1) {
                    $newToken = $this->renewToken($session['user_id'], $token);
                    $updatedToken = true;
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
            $newExpireAt = $this->formatDate('+30 days'); // Génération de la nouvelle date d'expiration

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

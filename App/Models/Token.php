<?php

namespace Models;

use App\Database;
use DateTime;
use DateTimeZone;
use IntlDateFormatter;

class Token
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function verifyToken($token)
    {
        try {
            // Requête pour obtenir la session associée au token
            $request = "SELECT * FROM sessions WHERE token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$token]);

            // Récupération de la session
            $session = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($session) {
                $currentTime = new DateTime();
                $expireTime = new DateTime($session['expire_at']);

                // Vérification de l'expiration du token
                if ($expireTime <= $currentTime) {
                    return ["success" => false, "message" => "Token expired or invalid"];
                }

                // Retourner les informations du token et de l'utilisateur
                return [
                    "success" => true,
                    "message" => "Token is valid",
                    "token" => $token,
                    "user_id" => $session['user_id'] // Retourne l'ID utilisateur pour identifier qui est connecté
                ];
            } else {
                return ["success" => false, "message" => "Token expired or invalid"];
            }
        } catch (\PDOException $e) {
            error_log("Error when verifying token: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while verifying the token"];
        }
    }

    public function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    public function formatDate($interval, $format = 'yyyy-MM-dd HH:mm:ss', $timezone = 'Europe/Paris', $locale = 'fr_FR')
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

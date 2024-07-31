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
            $request = "SELECT * FROM sessions WHERE token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$token]);

            $session = $pdo->fetch(\PDO::FETCH_ASSOC);

            if ($session) {
                $currentTime = new DateTime();
                $expireTime = new DateTime($session['expire_at']);
                $newToken = $token;
                $updatedToken = false;

                if ($expireTime <= $currentTime || $currentTime->diff($expireTime)->i < 1) {
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
                return ["success" => false, "message" => "Token expired or invalid"];
            }
        } catch (\PDOException $e) {
            error_log("Error when verifying token: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while verifying the token"];
        }
    }

    public function renewToken($userId, $oldToken)
    {
        try {
            $newToken = $this->generateToken();
            $newExpireAt = $this->formatDate('+2 minutes');

            $request = "UPDATE sessions SET token = ?, expire_at = ? WHERE user_id = ? AND token = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$newToken, $newExpireAt, $userId, $oldToken]);

            return $newToken;
        } catch (\PDOException $e) {
            error_log("Error when renewing token: " . $e->getMessage());
            http_response_code(500);
            return ["success" => false, "message" => "An error occurred while renewing the token"];
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
?>

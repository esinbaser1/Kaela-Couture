<?php

namespace Utils;

use Firebase\JWT\JWT;
use Dotenv\Dotenv;

// This class generates a JWT token
class Token
{
    public function __construct()
    {
        // Loads environment variables (like the JWT secret key) from the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    // Generates a JWT with username, role, and user_id
    public function generateToken($userId, $userRole, $username, $email)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (7 * 86400); // Le token expire après 7 jours

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $userId,
            'role' => $userRole,
            'username' => $username,
            'email' => $email, // Adds the username to the payload
        ];

        // Generate the JWT token using the secret key
        return JWT::encode($payload, $_ENV['JWT_SECRET_KEY'], 'HS256');
    }
}
<?php

namespace Models;

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
    public function generateToken($userId, $userRole, $username)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // The token expires after 1 hour

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $userId,
            'role' => $userRole,
            'username' => $username  // Adds the username to the payload
        ];

        // Generate the JWT token using the secret key
        return JWT::encode($payload, $_ENV['JWT_SECRET_KEY'], 'HS256');
    }
}
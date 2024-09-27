<?php

namespace Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

// This class handles user authentication and role-based access control using JWT tokens
class AuthUtils
{
    public function __construct()
    {
        // Loads environment variables (like the JWT secret key) from the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    // Extracts the JWT token from the 'Authorization' header of the HTTP request
    // Returns the token without the 'Bearer' prefix or null if the token is not present
    public function extractTokenFromHeaders()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            return str_replace('Bearer ', '', $headers['Authorization']);
        }
        return null;
    }

    // Checks the user's access to a specific route according to their role and checks the validity of the JWT token and its expiry date
    public function verifyAccess($requiredRole = 'admin')
    {
        $token = $this->extractTokenFromHeaders();

        // If no token is found, return a 401 Unauthorized response
        if (!$token) 
        {
            http_response_code(401);
            return ["success" => false, "message" => "Unauthorised access."];
        }

        try 
        {
            // Decodes the JWT token using the secret key from the environment variables
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));

            // Checks if the token has expired. If expired, return a 401 Unauthorized response
            if ($decoded->exp < time()) 
            {
                http_response_code(401);
                return ["success" => false, "message" => "Token expired."];
            }

            // Retrieves the user role from the token and checks if it matches the required role
            if (strtolower($decoded->role) !== strtolower($requiredRole)) 
            {
                http_response_code(403);
                return ["success" => false, "message" => "Insufficient rights."];
            }

            return null; // Access granted, no error returned
        } 
        catch (\Exception $e) 
        {
            // If any error occurs during token decoding, return a 401 Unauthorized response
            http_response_code(401);
            return ["success" => false, "message" => "Invalid or unauthorised token."];
        }
    }
}

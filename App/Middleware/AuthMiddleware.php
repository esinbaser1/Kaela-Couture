<?php

namespace App\Middleware;

use Models\Token;
use Models\Login;

class AuthMiddleware
{
    public static function verifyTokenAndRole($requiredRole = 'user')
    {
        // Récupérer le token depuis les en-têtes de la requête
        $headers = apache_request_headers();
        $token = $headers['Authorization'] ?? '';

        if (empty($token)) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Token not provided"]);
            exit();
        }

        $tokenModel = new Token();
        $result = $tokenModel->verifyToken($token);

        if (!$result['success']) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Invalid or expired token"]);
            exit();
        }

        // Vérifier le rôle de l'utilisateur
        $loginModel = new Login();
        $user = $loginModel->getUserById($result['user_id']);

        if ($user['role'] !== $requiredRole) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Insufficient permissions"]);
            exit();
        }
    }
}

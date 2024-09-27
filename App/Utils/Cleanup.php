<?php

namespace Utils;

use App\Database;
use PDOException;

class Cleanup
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection(); 
    }

    public function deleteUser()
    {
        try {
            // Suppression des utilisateurs inactifs depuis plus de 3 ans
            $thresholdDateUsers = date('Y-m-d H:i:s', strtotime('-3 years'));
    
            // Anonymisation des commentaires : on supprime la référence à l'utilisateur sans supprimer les commentaires
            $request = "UPDATE comment 
                        SET user_id = NULL
                        WHERE user_id IN (SELECT id FROM user WHERE last_active_at < ?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$thresholdDateUsers]);
    
            // Suppression des sessions liées aux utilisateurs inactifs
            $request = "DELETE s FROM sessions s
                        JOIN user u ON s.user_id = u.id
                        WHERE u.last_active_at < ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$thresholdDateUsers]);
    
            // Suppression des utilisateurs inactifs
            $request = "DELETE FROM user WHERE last_active_at < ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$thresholdDateUsers]);
    
            $deletedUsers = $pdo->rowCount();
    
            // Retourner les résultats
            return [
                "deletedUsers" => $deletedUsers,
            ];
    
        } catch (PDOException $e) {
            error_log("Erreur lors du nettoyage de la base de données : " . $e->getMessage());
            echo "Erreur lors du nettoyage de la base de données : " . $e->getMessage();
            return ["error" => "Erreur lors du nettoyage de la base de données."];
        }
    }
    

    
}

<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT 1");
    if ($stmt) {
        echo "Connexion à la base de données réussie !";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
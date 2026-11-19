<?php
session_start();
require_once 'config.php';

// Vérifier si le professeur est connecté
$teacherId = getCurrentTeacherId($pdo);
if (!$teacherId) {
    echo json_encode(['success' => false, 'message' => 'Aucun professeur connecté.']);
    exit;
}

// Récupérer les données envoyées
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['scheduleData']) || empty($data['scheduleData'])) {
    echo json_encode(['success' => false, 'message' => 'Données de l\'emploi du temps manquantes.']);
    exit;
}

try {
    // Supprimer l'ancien emploi du temps du professeur (optionnel, selon la logique souhaitée)
    $pdo->prepare("DELETE FROM emploie WHERE teacher_id = ?")->execute([$teacherId]);
    error_log("Ancien emploi du temps supprimé pour teacher_id $teacherId.");

    // Insérer le nouvel emploi du temps
    $stmt = $pdo->prepare("INSERT INTO emploie (teacher_id, schedule_data) VALUES (?, ?)");
    $stmt->execute([$teacherId, json_encode($data['scheduleData'])]);
    error_log("Nouvel emploi du temps enregistré pour teacher_id $teacherId.");

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Erreur lors de l'enregistrement pour teacher_id $teacherId : " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>
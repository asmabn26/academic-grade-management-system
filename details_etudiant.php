<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$studentId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
if ($studentId === null || $studentId === false) {
    die("ID d'étudiant invalide.");
}

// Récupérer les informations de l'étudiant et ses notes
getCurrentSurveillantId($pdo); // Juste pour vérifier la session, pas utilisé pour filtrer
$sql = "SELECT s.*, n.noteExamen1, n.noteExamen2, n.noteExamen3 
        FROM students s 
        LEFT JOIN notes n ON s.id = n.id 
        WHERE s.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Étudiant non trouvé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Étudiant - GradeManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e2a44;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            background: linear-gradient(-45deg, #e6e9f0, #d1d5e0, #c3daff, #e6e9f0);
            background-size: 400%;
            animation: gradientShift 15s ease infinite;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://i.pinimg.com/736x/0c/ea/6f/0cea6fabe4d9d5256e0d1b2474927732.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            filter: blur(10px);
            z-index: -1;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            margin-left: 270px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header h2 {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 2rem;
            margin-bottom: 20px;
            position: relative;
        }
        .header h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #1e3a8a;
            border-radius: 2px;
        }
        .student-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #1e3a8a;
            margin-bottom: 20px;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-item strong {
            color: #1e3a8a;
            margin-right: 10px;
        }
        .note-item {
            margin-bottom: 10px;
        }
        .note-item strong {
            color: #1e3a8a;
            margin-right: 10px;
        }
        .back-btn {
            background: #1e3a8a;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Détails de l'Étudiant</h2>
    </div>
    <div class="text-center">
        <img src="<?php echo htmlspecialchars($student['photo'] ?: 'https://via.placeholder.com/120'); ?>" alt="<?php echo htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?>" class="student-photo">
        <h3><?php echo htmlspecialchars($student['lastName'] . ' ' . $student['firstName']); ?></h3>
    </div>
    <div class="mt-4">
        <div class="detail-item"><strong>CNE :</strong> <?php echo htmlspecialchars($student['CNE']); ?></div>
        <div class="detail-item"><strong>Filière :</strong> <?php echo htmlspecialchars($student['filiere']); ?></div>
        <div class="detail-item"><strong>Niveau :</strong> <?php echo htmlspecialchars($student['niveau']); ?></div>
        <h4 class="mt-4">Notes des Modules (par Professeur)</h4>
        <?php
        // Récupérer toutes les notes pour cet étudiant
        $sqlNotes = "SELECT e.nom, e.prenom, n.noteExamen1, n.noteExamen2, n.noteExamen3 
                     FROM notes n 
                     JOIN enseignants e ON n.idp = e.idp 
                     WHERE n.id = ?";
        $stmtNotes = $pdo->prepare($sqlNotes);
        $stmtNotes->execute([$studentId]);
        $notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

        if (empty($notes)) {
            echo "<p>Aucune note disponible pour cet étudiant.</p>";
        } else {
            foreach ($notes as $note) {
                echo "<div class='note-item'><strong>Professeur :</strong> " . htmlspecialchars($note['prenom'] . ' ' . $note['nom']) . "<br>";
                echo "<strong>Examen 1 :</strong> " . ($note['noteExamen1'] !== null ? number_format($note['noteExamen1'], 2) : 'Non disponible') . "<br>";
                echo "<strong>Examen 2 :</strong> " . ($note['noteExamen2'] !== null ? number_format($note['noteExamen2'], 2) : 'Non disponible') . "<br>";
                echo "<strong>Examen 3 :</strong> " . ($note['noteExamen3'] !== null ? number_format($note['noteExamen3'], 2) : 'Non disponible') . "</div>";
            }
        }
        ?>
    </div>
    <div class="mt-4 text-center">
        <a href="gestion_etudiants.php" class="back-btn">Retour</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
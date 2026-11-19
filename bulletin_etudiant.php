<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
require_once 'fpdf186/fpdf.php'; // Include FPDF library

$studentId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
if ($studentId === null || $studentId === false) {
    die("ID d'étudiant invalide.");
}

// Récupérer les informations de l'étudiant
$sql = "SELECT s.* FROM students s WHERE s.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Étudiant non trouvé.");
}

// Récupérer les notes et informations des professeurs
$sqlNotes = "SELECT e.nom, e.prenom, e.module, n.noteExamen1, n.noteExamen2, n.noteExamen3 
             FROM notes n 
             JOIN enseignants e ON n.idp = e.idp 
             WHERE n.id = ?";
$stmtNotes = $pdo->prepare($sqlNotes);
$stmtNotes->execute([$studentId]);
$notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

// Function to determine mention based on average
function getMention($average) {
    if ($average < 10) return "Échec";
    if ($average < 12) return "Passable";
    if ($average < 14) return "Assez Bien";
    if ($average < 16) return "Bien";
    if ($average < 18) return "Très Bien";
    return "Excellent";
}

// Handle PDF generation
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    class PDF extends FPDF {
        // Fonction pour convertir UTF-8 en encodage compatible avec FPDF
        function convertToPDFEncoding($text) {
            return iconv('UTF-8', 'Windows-1252', $text);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    
    // Title
    $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Bulletin de Notes'), 0, 1, 'C');
    $pdf->Ln(10);
    
    // Student Information
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Nom: ' . $student['lastName'] . ' ' . $student['firstName']), 0, 1);
    $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('CNE: ' . $student['CNE']), 0, 1);
    $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Filière: ' . $student['filiere']), 0, 1);
    $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Niveau: ' . $student['niveau']), 0, 1);
    $pdf->Ln(10);
    
    // Grades Table
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 10, $pdf->convertToPDFEncoding('Module'), 1);
    $pdf->Cell(40, 10, $pdf->convertToPDFEncoding('Professeur'), 1);
    $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Examen 1'), 1);
    $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Examen 2'), 1);
    $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Examen 3'), 1);
    $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Moyenne'), 1);
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 10);
    $totalAverage = 0;
    $moduleCount = 0;
    
    foreach ($notes as $note) {
        $grades = array_filter([
            $note['noteExamen1'],
            $note['noteExamen2'],
            $note['noteExamen3']
        ], function($grade) { return $grade !== null; });
        $average = !empty($grades) ? array_sum($grades) / count($grades) : null;
        if ($average !== null) {
            $totalAverage += $average;
            $moduleCount++;
        }
        
        $pdf->Cell(40, 10, $pdf->convertToPDFEncoding($note['module'] ?: 'Non spécifié'), 1);
        $pdf->Cell(40, 10, $pdf->convertToPDFEncoding($note['prenom'] . ' ' . $note['nom']), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($note['noteExamen1'] !== null ? number_format($note['noteExamen1'], 2) : 'N/A'), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($note['noteExamen2'] !== null ? number_format($note['noteExamen2'], 2) : 'N/A'), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($note['noteExamen3'] !== null ? number_format($note['noteExamen3'], 2) : 'N/A'), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($average !== null ? number_format($average, 2) : 'N/A'), 1);
        $pdf->Ln();
    }
    
    // General Average and Mention
    if ($moduleCount > 0) {
        $generalAverage = $totalAverage / $moduleCount;
        $mention = getMention($generalAverage);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, 10, $pdf->convertToPDFEncoding('Moyenne Générale:'), 1);
        $pdf->Cell(20, 10, number_format($generalAverage, 2), 1);
        $pdf->Cell(40, 10, $pdf->convertToPDFEncoding('Mention: ' . $mention), 1);
    }
    
    // Output PDF
    $pdf->Output('D', 'Bulletin_' . $student['lastName'] . '_' . $student['firstName'] . '.pdf');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Notes - GradeManager</title>
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
            max-width: 900px;
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
        .table {
            margin-top: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table th {
            background: #1e3a8a;
            color: #ffffff;
        }
        .table .average {
            font-weight: bold;
            color: #1e3a8a;
        }
        .mention {
            font-weight: bold;
            color: #dc3545;
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
        .print-btn {
            background: #28a745;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .print-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .download-btn {
            background: #dc3545;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Bulletin de Notes</h2>
    </div>
    <div class="text-center">
        <img src="<?php echo htmlspecialchars($student['photo'] ?: 'https://via.placeholder.com/120'); ?>" 
             alt="<?php echo htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?>" 
             class="student-photo">
        <h3><?php echo htmlspecialchars($student['lastName'] . ' ' . $student['firstName']); ?></h3>
    </div>
    <div class="mt-4">
        <div class="detail-item"><strong>CNE :</strong> <?php echo htmlspecialchars($student['CNE']); ?></div>
        <div class="detail-item"><strong>Filière :</strong> <?php echo htmlspecialchars($student['filiere']); ?></div>
        <div class="detail-item"><strong>Niveau :</strong> <?php echo htmlspecialchars($student['niveau']); ?></div>
    </div>
    <h4 class="mt-4">Notes des Modules</h4>
    <?php if (empty($notes)): ?>
        <p>Aucune note disponible pour cet étudiant.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Professeur</th>
                    <th>Examen 1</th>
                    <th>Examen 2</th>
                    <th>Examen 3</th>
                    <th>Moyenne</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalAverage = 0;
                $moduleCount = 0;
                foreach ($notes as $note): 
                    $grades = array_filter([
                        $note['noteExamen1'],
                        $note['noteExamen2'],
                        $note['noteExamen3']
                    ], function($grade) { return $grade !== null; });
                    $average = !empty($grades) ? array_sum($grades) / count($grades) : null;
                    if ($average !== null) {
                        $totalAverage += $average;
                        $moduleCount++;
                    }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($note['module'] ?: 'Non spécifié'); ?></td>
                        <td><?php echo htmlspecialchars($note['prenom'] . ' ' . $note['nom']); ?></td>
                        <td><?php echo $note['noteExamen1'] !== null ? number_format($note['noteExamen1'], 2) : 'N/A'; ?></td>
                        <td><?php echo $note['noteExamen2'] !== null ? number_format($note['noteExamen2'], 2) : 'N/A'; ?></td>
                        <td><?php echo $note['noteExamen3'] !== null ? number_format($note['noteExamen3'], 2) : 'N/A'; ?></td>
                        <td class="average">
                            <?php echo $average !== null ? number_format($average, 2) : 'N/A'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($moduleCount > 0): ?>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Moyenne Générale :</strong></td>
                        <td class="average"><?php echo number_format($totalAverage / $moduleCount, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end"><strong>Mention :</strong></td>
                        <td class="mention"><?php echo getMention($totalAverage / $moduleCount); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <div class="mt-4 text-center">
        <a href="gestion_etudiants.php" class="back-btn">Retour</a>
        <a href="javascript:window.print()" class="print-btn ms-3">Imprimer</a>
        <a href="?id=<?php echo $studentId; ?>&download=pdf" class="download-btn ms-3">Télécharger PDF</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
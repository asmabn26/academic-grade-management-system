<?php
// Activer le rapport d'erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Nettoyer tout buffer existant
if (ob_get_length()) {
    ob_end_clean();
}

// Définir l'encodage UTF-8 pour PHP
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

// Inclure FPDF avec vérification
$fpdfPath = __DIR__ . '/fpdf186/fpdf.php';
if (!file_exists($fpdfPath)) {
    die('Erreur : Le fichier fpdf186/fpdf.php est introuvable. Vérifiez le chemin : ' . $fpdfPath);
}
require_once $fpdfPath;

require_once 'config.php';

// Vérifier si la classe est spécifiée
$class = $_GET['class'] ?? '';
if (!$class) {
    die('Erreur : Aucun nom de classe spécifié. Utilisez ?class=Filiere+Niveau (ex. ?class=GCDSTE+C1)');
}

// Séparer filiere et niveau
$classParts = preg_split('/[\s+]+/', trim($class));
if (count($classParts) !== 2) {
    die('Erreur : Le format de la classe est incorrect. Utilisez ?class=Filiere+Niveau (ex. ?class=GCDSTE+C1)');
}
$filiere = trim($classParts[0]);
$niveau = trim($classParts[1]);

// Classe personnalisée FPDF avec en-tête
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $this->convertToPDFEncoding('Bulletin de Notes'), 0, 1, 'C');
        $this->Ln(10);
    }

    // Fonction pour convertir UTF-8 en encodage compatible avec FPDF
    function convertToPDFEncoding($text) {
        return iconv('UTF-8', 'Windows-1252', $text);
    }
}

// Fonction pour déterminer la mention
function getMention($average) {
    if ($average === null || $average < 10) return "Échec";
    if ($average < 12) return "Passable";
    if ($average < 14) return "Assez Bien";
    if ($average < 16) return "Bien";
    if ($average < 18) return "Très Bien";
    return "Excellent";
}

// Créer un dossier temporaire
$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('bulletins_');
if (!mkdir($tempDir, 0777, true) && !is_dir($tempDir)) {
    die('Erreur : Impossible de créer le dossier temporaire. Vérifiez les permissions de ' . sys_get_temp_dir());
}

$bulletinDir = $tempDir . DIRECTORY_SEPARATOR . $filiere;
if (!mkdir($bulletinDir, 0777, true) && !is_dir($bulletinDir)) {
    die('Erreur : Impossible de créer le dossier pour la filière.');
}

// Année scolaire dynamique
$currentMonth = (int)date('m');
$currentYear = (int)date('Y');
$academicYear = $currentMonth >= 9 ? "$currentYear-" . ($currentYear + 1) : ($currentYear - 1) . "-$currentYear";

try {
    // Récupérer les étudiants
    $stmt = $pdo->prepare("SELECT * FROM students WHERE LOWER(TRIM(filiere)) = LOWER(?) AND LOWER(TRIM(niveau)) = LOWER(?)");
    $stmt->execute([$filiere, $niveau]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($students)) {
        die("Aucun étudiant trouvé dans la classe $filiere $niveau. Vérifiez les données dans la table 'students'.");
    }

    // Générer un bulletin pour chaque étudiant
    foreach ($students as $student) {
        $pdf = new PDF();
        $pdf->SetFont('Arial', '', 12);
        $pdf->AddPage();

        // Récupérer les notes
        $stmtNotes = $pdo->prepare("
            SELECT e.nom, e.prenom, e.module, n.noteExamen1, n.noteExamen2, n.noteExamen3
            FROM notes n
            JOIN enseignants e ON n.idp = e.idp
            WHERE n.id = ?
        ");
        $stmtNotes->execute([$student['id']]);
        $notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

        // Calculer la moyenne générale
        $totalAverage = 0;
        $moduleCount = 0;
        foreach ($notes as &$note) {
            $grades = array_filter([
                $note['noteExamen1'],
                $note['noteExamen2'],
                $note['noteExamen3']
            ], function($grade) { return $grade !== null; });
            $note['average'] = !empty($grades) ? array_sum($grades) / count($grades) : null;
            if ($note['average'] !== null) {
                $totalAverage += $note['average'];
                $moduleCount++;
            }
        }
        unset($note);
        $generalAverage = $moduleCount > 0 ? $totalAverage / $moduleCount : null;
        $mention = $generalAverage !== null ? getMention($generalAverage) : '-';

        // Informations de l'étudiant (convertir en Windows-1252)
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Nom de l\'étudiant : ' . $student['lastName'] . ' ' . $student['firstName']), 0, 1);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('CNE : ' . $student['CNE']), 0, 1);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Classe : ' . $student['filiere'] . ' ' . $student['niveau']), 0, 1);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Moyenne générale : ' . ($generalAverage !== null ? number_format($generalAverage, 2) : 'Non disponible') . '/20'), 0, 1);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Mention : ' . $mention), 0, 1);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Année scolaire : ' . $academicYear), 0, 1);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Bulletin édité le : ' . date('d/m/Y')), 0, 1);
        $pdf->Ln(10);

        // Tableau des notes
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 10, $pdf->convertToPDFEncoding('Module'), 1);
        $pdf->Cell(40, 10, $pdf->convertToPDFEncoding('Enseignant'), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Examen 1'), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Examen 2'), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Examen 3'), 1);
        $pdf->Cell(20, 10, $pdf->convertToPDFEncoding('Moyenne'), 1);
        $pdf->Cell(30, 10, $pdf->convertToPDFEncoding('Appréciation'), 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        foreach ($notes as $note) {
            $pdf->Cell(40, 10, $pdf->convertToPDFEncoding($note['module'] ?: 'Non spécifié'), 1);
            $pdf->Cell(40, 10, $pdf->convertToPDFEncoding($note['prenom'] . ' ' . $note['nom']), 1);
            $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($note['noteExamen1'] !== null ? number_format($note['noteExamen1'], 2) : 'N/A'), 1);
            $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($note['noteExamen2'] !== null ? number_format($note['noteExamen2'], 2) : 'N/A'), 1);
            $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($note['noteExamen3'] !== null ? number_format($note['noteExamen3'], 2) : 'N/A'), 1);
            $pdf->Cell(20, 10, $pdf->convertToPDFEncoding($note['average'] !== null ? number_format($note['average'], 2) : 'N/A'), 1);
            $pdf->Cell(30, 10, $pdf->convertToPDFEncoding($note['average'] !== null ? getMention($note['average']) : '-'), 1);
            $pdf->Ln();
        }

        // Résultat final
        $pdf->Ln(10);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Décision du conseil : ' . ($generalAverage !== null && $generalAverage >= 10 ? 'Admis(e) avec mention' : 'Non admis(e)')), 0, 1);
        $pdf->Cell(0, 10, $pdf->convertToPDFEncoding('Signature du directeur : ____________________'), 0, 1);

        // Sauvegarder le PDF
        $filename = $bulletinDir . DIRECTORY_SEPARATOR . str_replace(' ', '_', $student['lastName'] . '_' . $student['firstName']) . '.pdf';
        $pdf->Output($filename, 'F');
    }

    // Créer une archive ZIP
    $zip = new ZipArchive();
    $zipFile = $tempDir . DIRECTORY_SEPARATOR . strtolower($filiere) . '_bulletins.zip';
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        die('Erreur : Impossible de créer le fichier ZIP. Vérifiez que l\'extension Zip est activée.');
    }

    // Ajouter les PDF au ZIP
    $files = glob($bulletinDir . DIRECTORY_SEPARATOR . '*.pdf');
    if (empty($files)) {
        die('Erreur : Aucun fichier PDF généré. Vérifiez les données ou les permissions.');
    }
    foreach ($files as $file) {
        $zip->addFile($file, $filiere . '/' . basename($file));
    }
    $zip->close();

    // Télécharger le ZIP
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
    header('Content-Length: ' . filesize($zipFile));
    readfile($zipFile);

    // Nettoyer les fichiers temporaires
    foreach (glob($bulletinDir . DIRECTORY_SEPARATOR . '*.pdf') as $file) {
        if (is_writable($file)) {
            unlink($file);
        }
    }
    rmdir($bulletinDir);
    if (is_writable($zipFile)) {
        unlink($zipFile);
    }
    rmdir($tempDir);

} catch (PDOException $e) {
    if (ob_get_length()) {
        ob_end_clean();
    }
    die("Erreur de base de données : " . $e->getMessage());
} catch (Exception $e) {
    if (ob_get_length()) {
        ob_end_clean();
    }
    die("Erreur générale : " . $e->getMessage());
}
?>
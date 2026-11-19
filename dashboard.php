<?php
// Ensure this file is only included within home.php
if (!defined('PHP_VERSION_ID')) {
    exit('Direct access not permitted');
}
session_start();

// Inclure la connexion à la base de données
require_once 'config.php';

// Récupérer l'identifiant de l'enseignant connecté
$ne = $_SESSION['ne'] ?? null;
if (!$ne) {
    die("Erreur : Numéro Enseignant (NE) non trouvé dans la session.");
}

// Récupérer le module de l'enseignant
$stmt = $pdo->prepare("SELECT module FROM enseignants WHERE ne = ?");
$stmt->execute([$ne]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
$teacherModule = $teacher['module'] ?? null;
if (!$teacherModule) {
    die("Erreur : Module non trouvé pour cet enseignant.");
}

// Récupérer le nombre total d'étudiants
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM students");
    $totalStudents = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage();
}

// Récupérer le nombre de classes (combinaisons uniques de filière et niveau)
$stmt = $pdo->query("SELECT COUNT(DISTINCT CONCAT(filiere, '-', niveau)) as total FROM students");
if ($stmt === false) {
    die("Erreur dans la requête SQL");
}
$totalClasses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Calculer la moyenne pour le module de l'enseignant connecté
$stmt = $pdo->prepare("
    SELECT AVG(student_avg.moyenne) as moyenne
    FROM (
        SELECT s.id, AVG(COALESCE(n.noteExamen1, 0) + COALESCE(n.noteExamen2, 0) + COALESCE(n.noteExamen3, 0)) / 3 as moyenne
        FROM students s
        LEFT JOIN notes n ON s.id = n.id
        LEFT JOIN enseignants e ON n.idp = e.idp
        WHERE e.module = ? AND e.ne = ?
        GROUP BY s.id
    ) student_avg
");
$stmt->execute([$teacherModule, $ne]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$averageGrade = $row['moyenne'] !== null ? number_format($row['moyenne'], 2) : '0.00';

// Récupérer les moyennes par classe pour le module de l'enseignant connecté
$stmt = $pdo->prepare("
    SELECT s.filiere, s.niveau, AVG(student_avg.moyenne) as moyenne
    FROM students s
    LEFT JOIN (
        SELECT n.id, AVG(COALESCE(n.noteExamen1, 0) + COALESCE(n.noteExamen2, 0) + COALESCE(n.noteExamen3, 0)) / 3 as moyenne
        FROM notes n
        LEFT JOIN enseignants e ON n.idp = e.idp
        WHERE e.module = ? AND e.ne = ?
        GROUP BY n.id
    ) student_avg ON s.id = student_avg.id
    GROUP BY s.filiere, s.niveau
    HAVING COUNT(student_avg.id) > 0 AND moyenne IS NOT NULL
    ORDER BY moyenne DESC
    LIMIT 5
");
$stmt->execute([$teacherModule, $ne]);
$classAverages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les 5 meilleurs étudiants pour le module de l'enseignant connecté
$stmt = $pdo->prepare("
    SELECT s.lastName, s.firstName, s.filiere, s.niveau, AVG(COALESCE(n.noteExamen1, 0) + COALESCE(n.noteExamen2, 0) + COALESCE(n.noteExamen3, 0)) / 3 as moyenne
    FROM students s
    LEFT JOIN notes n ON s.id = n.id
    LEFT JOIN enseignants e ON n.idp = e.idp
    WHERE e.module = ? AND e.ne = ?
    GROUP BY s.id, s.lastName, s.firstName, s.filiere, s.niveau
    HAVING COUNT(n.id) > 0 AND moyenne IS NOT NULL
    ORDER BY moyenne DESC
    LIMIT 5
");
$stmt->execute([$teacherModule, $ne]);
$topStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="welcome-card">
    <h2>Bienvenue dans votre espace enseignant</h2>
    <p>Utilisez les différentes fonctionnalités du menu pour gérer vos classes et vos étudiants.</p>
</div>

<div class="stats-container">
    <div class="stat-card">
        <h3>Étudiants</h3>
        <div class="value"><?php echo $totalStudents; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Classes</h3>
        <div class="value"><?php echo $totalClasses; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Moyenne (<?php echo htmlspecialchars(ucfirst($teacherModule)); ?>)</h3>
        <div class="value"><?php echo $averageGrade; ?></div>
    </div>
</div>

<div class="dashboard-row">
    <div class="chart-container">
        <h3>Répartition des notes par classe (<?php echo htmlspecialchars(ucfirst($teacherModule)); ?>)</h3>
        <?php if (empty($classAverages)): ?>
            <p>Aucune donnée disponible pour le module <?php echo htmlspecialchars(ucfirst($teacherModule)); ?>.</p>
        <?php else: ?>
            <canvas id="gradesChart"></canvas>
        <?php endif; ?>
    </div>
    
    <div class="top-students-container">
        <h3>Top 5 des étudiants (<?php echo htmlspecialchars(ucfirst($teacherModule)); ?>)</h3>
        <div class="top-students-list">
            <?php if (empty($topStudents)): ?>
                <p>Aucun étudiant trouvé pour le module <?php echo htmlspecialchars(ucfirst($teacherModule)); ?>.</p>
            <?php else: ?>
                <?php foreach ($topStudents as $index => $student): ?>
                    <div class="top-student-item">
                        <div class="rank"><?php echo $index + 1; ?></div>
                        <div class="student-info">
                            <div class="student-name"><?php echo htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?></div>
                            <div class="student-class"><?php echo htmlspecialchars($student['filiere'] . ' - ' . $student['niveau']); ?></div>
                        </div>
                        <div class="student-average"><?php echo number_format($student['moyenne'], 2); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const textColor = currentTheme === 'dark' ? '#f5f5f5' : '#333';
    
    const ctx = document.getElementById('gradesChart')?.getContext('2d');
    if (ctx && <?php echo count($classAverages) > 0 ? 'true' : 'false'; ?>) {
        const gradesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    <?php foreach ($classAverages as $class): ?>
                        '<?php echo htmlspecialchars($class['filiere'] . ' ' . $class['niveau']); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Moyenne par classe (<?php echo htmlspecialchars(ucfirst($teacherModule)); ?>)',
                    data: [
                        <?php foreach ($classAverages as $class): ?>
                            <?php echo $class['moyenne'] !== null ? number_format($class['moyenne'], 2) : 0; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 20,
                        ticks: { color: textColor, stepSize: 2 },
                        grid: { color: currentTheme === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)' }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { color: currentTheme === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)' }
                    }
                },
                plugins: {
                    legend: { display: false, labels: { color: textColor } },
                    title: { color: textColor }
                }
            }
        });
    }
});
</script>

<style>
.dashboard-row {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.chart-container, .top-students-container {
    flex: 1;
    background-color: var(--card-bg);
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    padding: 20px;
}

.top-students-container h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
    font-size: 18px;
    text-align: center;
}

.top-students-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.top-student-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 8px;
    background-color: rgba(255, 255, 255, 0.05);
    transition: transform 0.3s ease;
}

.top-student-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.rank {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
}

.student-info {
    flex: 1;
}

.student-name {
    font-weight: 600;
    color: var(--primary-color);
}

.student-class {
    font-size: 12px;
    color: var(--muted-text);
}

.student-average {
    font-weight: bold;
    font-size: 18px;
    color: var(--primary-color);
    padding: 5px 10px;
    border-radius: 5px;
    background-color: rgba(30, 58, 138, 0.1);
}

@media (max-width: 768px) {
    .dashboard-row {
        flex-direction: column;
    }
}
</style>
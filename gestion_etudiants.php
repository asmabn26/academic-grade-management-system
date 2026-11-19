<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants - GradeManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --success-color: #047857;
            --light-color: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
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
        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            height: 100vh;
            position: fixed;
        }
        .sidebar .nav-link {
            color: #666;
            padding: 10px 15px;
            border-radius: var(--border-radius);
        }
        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: #fff;
        }
        .sidebar-footer {
            padding: 20px;
            text-align: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
            left: 0;
            margin: 0 auto;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 80%;
            font-weight: 500;
            margin: 0 auto;
            transform: translateX(5px);
        }
        .logout-btn:hover {
            background-color: #c0392b;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
        }
        .logout-btn i {
            margin-right: 8px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            margin-left: 270px; /* Adjusted to make space for the sidebar */
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
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            animation: slideInLeft 0.8s ease-out;
        }
        .header h2 {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 2rem;
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
            animation: underlineExpand 0.5s ease-out forwards 0.8s;
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes underlineExpand {
            to { width: 60px; }
        }
        .btn-add {
            background: #1e3a8a;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-add:hover:not(.disabled) {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .btn-add.disabled {
            background: #6b7280;
            cursor: not-allowed;
        }
        .btn-add::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }
        .btn-add:hover:not(.disabled)::before {
            left: 100%;
        }
        .btn-add:active:not(.disabled) {
            transform: scale(1.1);
            transition: transform 0.1s ease;
        }
        .search-bar {
            max-width: 350px;
            margin-bottom: 20px;
        }
        .search-bar .form-control {
            border-radius: 8px;
            padding: 10px;
            font-size: 1rem;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .search-bar .form-control:focus {
            box-shadow: 0 0 10px rgba(30, 58, 138, 0.3);
            border-color: #1e3a8a;
        }
        .search-bar .btn {
            border-radius: 8px;
            background: #1e3a8a;
            color: #ffffff;
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .search-bar .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .search-bar .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }
        .search-bar .btn:hover::before {
            left: 100%;
        }
        .search-bar .btn:active {
            transform: scale(1.1);
            transition: transform 0.1s ease;
        }
        .table {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table th {
            background: #1e3a8a;
            color: #ffffff;
            padding: 15px;
            font-weight: 600;
            border: none;
        }
        .table td {
            vertical-align: middle;
            padding: 15px;
            border: none;
            transition: background 0.3s ease;
        }
        .table tbody tr {
            animation: bounceIn 0.8s ease-out;
        }
        .table tbody tr:nth-child(odd) {
            background: #f8fafc;
        }
        .table tbody tr:hover {
            transform: scale(1.01);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background: #e6f0ff;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.95); }
            60% { opacity: 1; transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .student-photo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1e3a8a;
            transition: transform 0.3s ease;
        }
        .student-photo:hover {
            transform: scale(1.1);
        }
        .action-btn {
            padding: 8px 16px;
            font-size: 0.95rem;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .action-btn.btn-outline-primary {
            border-color: #1e3a8a;
            color: #1e3a8a;
        }
        .action-btn.btn-outline-success {
            border-color: #047857;
            color: #047857;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }
        .action-btn:hover::before {
            left: 100%;
        }
        .action-btn:active {
            transform: scale(1.1);
            transition: transform 0.1s ease;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            .container {
                margin-left: 0;
            }
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .search-bar {
                max-width: 100%;
            }
            .table th, .table td {
                padding: 10px;
                font-size: 0.9rem;
            }
            .student-photo {
                width: 36px;
                height: 36px;
            }
            .action-btn {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
<?php
require_once 'config.php';

// Récupérer les étudiants depuis la base de données
$sql = "SELECT id, lastName, firstName, CNE, filiere, niveau, photo FROM students ORDER BY lastName, firstName";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="sidebar">
    <h4 class="text-center mb-4">GradeManager</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="dashboard_secretaire.php"><i class="bi bi-house"></i> Tableau de bord</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="gestion_etudiants.php"><i class="bi bi-people"></i> Étudiants</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="bulletins.php"><i class="bi bi-file-earmark-text"></i> Bulletins</a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>
</div>

<div class="container">
    <div class="header">
        <h2>Gestion des Étudiants</h2>
        <a href="#" class="btn btn-add disabled" title="Ajout désactivé pour ce rôle">
            <i class="bi bi-person-plus"></i> Ajouter un étudiant
        </a>
    </div>

    <div class="input-group search-bar">
        <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un étudiant..." aria-label="Rechercher un étudiant">
        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('searchInput').focus();"><i class="bi bi-search"></i></button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>CNE</th>
                    <th>Filière</th>
                    <th>Niveau</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTable">
                <?php if (empty($students)): ?>
                    <tr><td colspan="6" class="text-center py-4">Aucun étudiant trouvé</td></tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($student['photo'] ?: 'https://via.placeholder.com/40'); ?>" alt="<?php echo htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?>" class="student-photo"></td>
                            <td><?php echo htmlspecialchars($student['lastName'] . ' ' . $student['firstName']); ?></td>
                            <td><?php echo htmlspecialchars($student['CNE']); ?></td>
                            <td><?php echo htmlspecialchars($student['filiere']); ?></td>
                            <td><?php echo htmlspecialchars($student['niveau']); ?></td>
                            <td>
                                <a href="details_etudiant.php?id=<?php echo $student['id']; ?>" class="btn btn-outline-primary action-btn">Détails</a>
                                <a href="bulletin_etudiant.php?id=<?php echo $student['id']; ?>" class="btn btn-outline-success action-btn">Bulletin</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#studentTable tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const name = cells[1]?.textContent.toLowerCase() || '';
            const cne = cells[2]?.textContent.toLowerCase() || '';
            const filiere = cells[3]?.textContent.toLowerCase() || '';
            const niveau = cells[4]?.textContent.toLowerCase() || '';

            if (name.includes(searchValue) || cne.includes(searchValue) || filiere.includes(searchValue) || niveau.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
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
    <title>Bulletins de notes - surveillant général</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --success-color: #047857;
            --light-color: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e6f0fa;
            color: #333;
            padding: 20px;
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
        .content {
            margin-left: 270px;
        }
        .card {
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
        }
        .card h2 {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-success {
            background: var(--success-color);
            border-color: var(--success-color);
        }
        .btn-group .btn {
            margin-right: 10px;
        }
        .filter-section {
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            .content {
                margin-left: 0;
            }
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
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center mb-4">Surveillant-Général</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard_secretaire.php"><i class="bi bi-house"></i> Tableau de bord</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="gestion_etudiants.php"><i class="bi bi-people"></i> Étudiants</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="bulletins.php"><i class="bi bi-file-earmark-text"></i> Bulletins</a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="card">
                <h2>Bulletins de notes</h2>
                <p>Générez et consultez les bulletins de notes des étudiants</p>
                <?php
                require_once 'config.php';
                $stmt = $pdo->query("SELECT filiere, niveau, COUNT(*) as count FROM students GROUP BY filiere, niveau");
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <div class="filter-section">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="bi bi-filter"></i> Filtrer
                        </button>
                    </div>
                </div>

                <!-- Modal pour filtrer -->
                <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filterModalLabel">Filtrer les bulletins</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="selection_bulletins.php" method="get">
                                    <div class="mb-3">
                                        <label for="classFilter" class="form-label">Sélectionner une classe</label>
                                        <select class="form-select" id="classFilter" name="class" required>
                                            <option value="">Sélectionner une classe</option>
                                            <?php foreach ($classes as $class): ?>
                                                <option value="<?php echo htmlspecialchars($class['filiere'] . ' ' . $class['niveau']); ?>">
                                                    <?php echo htmlspecialchars($class['filiere'] . ' ' . $class['niveau'] . ' (' . $class['count'] . ' étudiants)'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Appliquer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> Information importante : Les bulletins de notes générés incluent toutes les notes et moyennes des étudiants pour la période actuelle. Ils peuvent être téléchargés individuellement ou par classe au format PDF pour impression ou archivage.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
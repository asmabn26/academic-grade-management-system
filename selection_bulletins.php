<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection des Bulletins - GradeManager</title>
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
        .sidebar {
            width: 250px;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            height: 100vh;
            position: fixed;
            transition: transform 0.5s ease;
        }
        .sidebar:hover {
            transform: scale(1.02);
        }
        .sidebar .nav-link {
            color: #4b5563;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background: rgba(30, 58, 138, 0.1);
            color: #1e3a8a;
            transform: translateX(10px);
        }
        .sidebar .nav-link.active {
            background: #1e3a8a;
            color: #ffffff;
            box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.3);
        }
        .content {
            margin-left: 270px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
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
        .student-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .student-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(30, 58, 138, 0.2);
        }
        .student-item img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid #1e3a8a;
            transition: transform 0.3s ease;
        }
        .student-item img:hover {
            transform: scale(1.1);
        }
        .student-item a {
            margin-left: auto;
            text-decoration: none;
            padding: 8px 16px;
            font-size: 0.95rem;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .student-item a.btn-primary {
            background: #1e3a8a;
            color: #ffffff;
        }
        .student-item a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .student-item a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }
        .student-item a:hover::before {
            left: 100%;
        }
        .btn-success {
            background: #047857;
            border-color: #047857;
            color: #ffffff;
            padding: 12px 24px;
            width: 100%;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(4, 120, 87, 0.3);
        }
        .btn-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }
        .btn-success:hover::before {
            left: 100%;
        }
        .alert {
            background: rgba(248, 250, 252, 0.9);
            border-left: 5px solid #1e3a8a;
            border-radius: 8px;
            backdrop-filter: blur(3px);
            padding: 15px;
            animation: bounceIn 0.8s ease-out;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.95); }
            60% { opacity: 1; transform: scale(1.02); }
            100% { transform: scale(1); }
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
            .student-item {
                flex-direction: column;
                text-align: center;
            }
            .student-item img {
                margin-bottom: 10px;
            }
            .student-item a {
                margin-left: 0;
                margin-top: 10px;
            }
            .btn-success {
                width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center mb-4">GradeManager</h4>
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
    </div>

    <div class="content">
        <div class="container">
            <div class="header">
                <h2>Générer des bulletins</h2>
            </div>
            <p>Sélectionnez une classe pour générer les bulletins de notes des étudiants. Vous pouvez générer un bulletin individuel ou pour l'ensemble de la classe.</p>
            <?php
            require_once 'config.php';
            $class = $_GET['class'] ?? '';
            $students = [];
            if ($class) {
                list($filiere, $niveau) = explode(' ', $class, 2);
                $stmt = $pdo->prepare("SELECT id, lastName, firstName, photo, CNE, filiere, niveau FROM students WHERE filiere = ? AND niveau = ?");
                $stmt->execute([$filiere, $niveau]);
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            ?>
            <div class="mb-3">
                <strong>Sélectionner une classe :</strong> <?php echo htmlspecialchars($class ?: 'Aucune classe sélectionnée'); ?>
            </div>
            <?php if (!empty($students)): ?>
                <div class="mb-3">
                    <h4>Étudiants de <?php echo htmlspecialchars($class); ?></h4>
                    <?php foreach ($students as $student): ?>
                        <div class="student-item">
                            <img src="<?php echo htmlspecialchars($student['photo'] ?: 'https://via.placeholder.com/48'); ?>" alt="<?php echo htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?>">
                            <span><?php echo htmlspecialchars($student['lastName'] . ' ' . $student['firstName']); ?> (ID: <?php echo $student['id']; ?>)</span>
                            <a href="bulletin_etudiant.php?id=<?php echo $student['id']; ?>&class=<?php echo urlencode($class); ?>" class="btn btn-primary">Bulletin</a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="generate_class_bulletins.php?class=<?php echo urlencode($class); ?>" class="btn btn-success">
                    <i class="bi bi-file-earmark-pdf"></i> Générer tous les bulletins de la classe
                </a>
            <?php else: ?>
                <p>Aucun étudiant trouvé pour cette classe.</p>
            <?php endif; ?>
            <div class="alert alert-info mt-3" role="alert">
                <i class="bi bi-info-circle"></i> Information importante : Les bulletins de notes générés incluent toutes les notes et moyennes des étudiants pour la période actuelle. Ils peuvent être téléchargés individuellement ou par classe au format PDF pour impression ou archivage.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
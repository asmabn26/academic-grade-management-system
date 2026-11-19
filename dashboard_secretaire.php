<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['ne'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'utilisateur depuis la base de données
require_once 'config.php';
$ne = $_SESSION['ne'];
$stmt = $pdo->prepare("SELECT nom, prenom FROM surveillant WHERE ne = ?");
$stmt->execute([$ne]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $nom = $user['nom'];
    $prenom = $user['prenom'];
} else {
    $nom = 'Surveillant';
    $prenom = '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord du surveillant général</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e2a44;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
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
        .dashboard-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 900px;
            animation: fadeInUp 1s ease-out;
            position: relative;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            color: #1e3a8a;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 2.3rem;
            position: relative;
            animation: slideInLeft 0.8s ease-out;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
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
            to { width: 80px; }
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-info span {
            color: #4b5563;
            font-size: 1rem;
        }
        .user-info .user-name {
            font-weight: bold;
            color: #1e3a8a;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .subtitle {
            color: #4b5563;
            margin-bottom: 30px;
            font-size: 1rem;
            font-weight: 400;
            animation: fadeIn 1.2s ease-out 0.2s both;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: bounceIn 0.8s ease-out;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.8); }
            60% { opacity: 1; transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(30, 58, 138, 0.3);
            animation: glowPulse 1.5s infinite;
        }
        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 10px rgba(30, 58, 138, 0.3); }
            50% { box-shadow: 0 0 20px rgba(30, 58, 138, 0.5); }
        }
        .stat-card h3 {
            margin: 0;
            font-size: 2.2rem;
            color: #1e3a8a;
            font-weight: 600;
        }
        .stat-card p {
            margin: 5px 0 0;
            color: #4b5563;
            font-size: 0.9rem;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .card {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            min-height: 220px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: scaleIn 1s ease-out;
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        .card.green {
            background: linear-gradient(135deg, #047857, #10b981);
        }
        .card h4 {
            margin: 0 0 12px;
            font-weight: 600;
            font-size: 1.5rem;
        }
        .card p {
            margin: 0;
            font-size: 1rem;
            line-height: 1.6;
        }
        .card a {
            display: inline-block;
            margin-top: 20px;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            background: #1e3a8a;
            padding: 12px 24px;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            font-size: 1.1rem;
        }
        .card.green a {
            background: #047857;
        }
        .card a:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .card a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent
            );
            transition: 0.5s;
        }
        .card a:hover::before {
            left: 100%;
        }
        .card a:active {
            transform: scale(1.1);
            transition: transform 0.1s ease;
        }
        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
            .cards {
                grid-template-columns: 1fr;
            }
            .dashboard-container {
                padding: 20px;
            }
            .header {
                font-size: 1.8rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .card {
                min-height: 240px;
            }
            .card h4 {
                font-size: 1.3rem;
            }
            .card p {
                font-size: 0.95rem;
            }
            .card a {
                font-size: 1rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <span>Tableau de bord</span>
            <div class="user-info">
                <span>Bonjour, <span class="user-name"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></span></span>
                <span>(<?php echo htmlspecialchars($ne); ?>)</span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
        <div class="subtitle">Plateforme intuitive pour une gestion optimale des étudiants et des bulletins.</div>
        <?php
        $totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $classes = $pdo->query("SELECT COUNT(DISTINCT filiere, niveau) FROM students")->fetchColumn();
        $bulletins = 0; // À dynamiser si une table pour les bulletins existe
        ?>
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $totalStudents; ?></h3>
                <p>Total d'étudiants</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $classes; ?></h3>
                <p>Classes</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $bulletins; ?></h3>
                <p>Bulletins générés</p>
            </div>
        </div>
        <div class="cards">
            <div class="card">
                <h4>Gestion des étudiants</h4>
                <p>Consulter facilement les informations des étudiants.</p>
                <a href="gestion_etudiants.php">Gérer les étudiants</a>
            </div>
            <div class="card green">
                <h4>Bulletins scolaires</h4>
                <p>Générez et téléchargez des bulletins professionnels en quelques clics.</p>
                <a href="bulletins.php">Voir les bulletins</a>
            </div>
        </div>
    </div>
</body>
</html>
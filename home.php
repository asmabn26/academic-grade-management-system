<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit();
}

// Récupérer les informations de l'utilisateur
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$ne = $_SESSION['ne'];

// Gérer le changement de thème
if (isset($_POST['toggle_theme'])) {
    if (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') {
        $_SESSION['theme'] = 'light';
    } else {
        $_SESSION['theme'] = 'dark';
    }
}

// Définir le thème par défaut si non défini
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}

$theme = $_SESSION['theme'];

// Definir quelle page
$section = $_GET['section'] ?? 'dashboard';
$valid_sections = ['dashboard', 'grades', 'reports', 'schedule', 'vacation', 'gestion_notes'];
$section = in_array($section, $valid_sections) ? $section : 'dashboard';
$section_file = $section . '.php';
?>

<!DOCTYPE html>
<html lang="fr" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Professeur</title>
    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Ajout de Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-color: #f5f5f5;
            --text-color: #333;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #fff;
            --card-bg: #fff;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --header-bg: #fff;
            --primary-color: #1e3a8a;
            --secondary-color: #2c3e50;
            --hover-color: #2980b9;
            --danger-color: #e74c3c;
            --danger-hover: #c0392b;
            --muted-text: #7f8c8d;
            --border-color: rgba(0, 0, 0, 0.1);
            --chart-colors: rgba(52, 152, 219, 0.7), rgba(46, 204, 113, 0.7), rgba(155, 89, 182, 0.7), rgba(241, 196, 15, 0.7), rgba(231, 76, 60, 0.7);
        }
        
        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #f5f5f5;
            --sidebar-bg: #121212;
            --sidebar-text: #f5f5f5;
            --card-bg: #2c2c2c;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            --header-bg: #2c2c2c;
            --primary-color: #1e3a8a;
            --secondary-color: #f5f5f5;
            --hover-color: #2980b9;
            --danger-color: #e74c3c;
            --danger-hover: #c0392b;
            --muted-text: #bdc3c7;
            --border-color: rgba(255, 255, 255, 0.1);
            --chart-colors: rgba(52, 152, 219, 0.7), rgba(46, 204, 113, 0.7), rgba(155, 89, 182, 0.7), rgba(241, 196, 15, 0.7), rgba(231, 76, 60, 0.7);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
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
            filter: blur(10px); /* Applique l'effet de flou */
            z-index: -1; /* Place l'image derrière le contenu */
        }
        
        /* Barre latérale */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #ffffff, #e6f0ff);
            color: #666;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 100;
            border-radius: 20px 20px 20px 20px;
            padding: 20px;
        }
        
        .sidebar-header {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .sidebar-header h2 {
            font-size: 22px;
            margin-bottom: 5px;
            color: var(--primary-color);
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        
        .sidebar-header p {
            font-size: 14px;
            color: #7f8c8d;
            margin: 0;
            font-weight: 300;
        }
        
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }
        
        .menu-item {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
            border-radius: 12px;
            margin-bottom: 8px;
            color: #666;
            transition: all 0.3s ease;
            font-weight: 400;
        }
        
        .menu-item:hover {
            background: rgba(30, 58, 138, 0.08);
            color: var(--primary-color);
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
        }
        
        .menu-item.active {
            background: var(--primary-color);
            color: #fff;
            box-shadow: 0 4px 10px rgba(30, 58, 138, 0.3);
        }
        
        .menu-item i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .menu-item span {
            font-size: 15px;
            letter-spacing: 0.2px;
        }
        
        .sidebar-footer {
            padding: 20px;
            text-align: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            position: absolute;
            bottom: 0;
            width: calc(100% - 40px);
            left: 0;
        }
        
        .logout-btn {
            background-color: var(--danger-color);
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
            width: 100%;
            font-weight: 500;
        }
        
        .logout-btn:hover {
            background-color: var(--danger-hover);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
        }
        
        .logout-btn i {
            margin-right: 8px;
        }
        
        /* Contenu principal */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            background-color: var(--header-bg);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 24px;
            color: var(--secondary-color);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info span {
            margin-right: 15px;
            color: var(--muted-text);
        }
        
        .user-info .user-name {
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .theme-toggle {
            margin-left: 400px;
            background-color: transparent;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            border-radius: 50%;
            transition: background-color 0.3s;
        }
        
        .theme-toggle:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .container {
            padding: 20px 0;
        }
        
        .welcome-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .welcome-card h2 {
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .welcome-card p {
            color: var(--text-color);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .dashboard-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            transition: transform 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .dashboard-card h3 {
            color: var(--secondary-color);
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .dashboard-card h3 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .dashboard-card p {
            color: var(--muted-text);
            margin-bottom: 15px;
        }
        
        .dashboard-card .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .dashboard-card .btn:hover {
            background-color: var(--hover-color);
        }
        
        /* Sections de contenu */
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
        }
        
        /* Statistiques */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            text-align: center;
        }
        
        .stat-card h3 {
            color: var(--muted-text);
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .chart-container {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .chart-container h3 {
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        /* Formulaires */
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background-color: var(--card-bg);
            color: var(--text-color);
            margin-bottom: 10px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        /* Animation du basculement de thème */
        .theme-transition {
            transition: background-color 0.5s ease, color 0.5s ease, border-color 0.5s ease, box-shadow 0.5s ease;
        }
         table {
            border-collapse: collapse;
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .file-input-container {
            text-align: center;
            margin: 20px auto;
        }
        .file-input-container input[type="file"] {
            display: none;
        }
        .file-input-container label {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            margin-top: 50px;

        }
        .file-input-container label:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
        }
        h1 {
            text-align: center;
            margin-top: 40px;
            color:#0056b3; ;
        }
    </style>
</head>
<body>
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Bonjour</h2>
            <p>Espace Enseignant</p>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-item <?php echo $section === 'dashboard' ? 'active' : ''; ?>" data-section="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </div>
            
            <div class="menu-item <?php echo $section === 'gestion_notes' ? 'active' : ''; ?>" data-section="gestion_notes">
                <i class="fas fa-graduation-cap"></i>
                <span>Gestion des notes</span>
            </div>
            
            <div class="menu-item <?php echo $section === 'schedule' ? 'active' : ''; ?>" data-section="schedule">
                <i class="fas fa-calendar-alt"></i>
                <span>Emploi du temps</span>
            </div>
            
            <div class="menu-item <?php echo $section === 'vacation' ? 'active' : ''; ?>" data-section="vacation">
                <i class="fas fa-umbrella-beach"></i>
                <span>Vacances</span>
            </div>
        </div>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="main-content">
        <div class="header">
            <div class="user-info">
                <span>Bonjour, <span class="user-name">Prof. <?php echo htmlspecialchars($prenom . ' ' . $nom); ?></span></span>
                <span>NE: <?php echo htmlspecialchars($ne); ?></span>
                
                <!-- Bouton de basculement de thème -->
                <form method="post" style="display: inline;">
                    <button type="submit" name="toggle_theme" class="theme-toggle" title="Changer de thème">
                        <?php if ($theme === 'dark'): ?>
                            <i class="fas fa-sun"></i>
                        <?php else: ?>
                            <i class="fas fa-moon"></i>
                        <?php endif; ?>
                        </button>
                </form>
            </div>
        </div>

        <div class="container">
            <?php
            if (file_exists($section_file)) {
                include $section_file;
            } else {
                echo '<p>Section non trouvée.</p>';
            }
            ?>
        </div>
    </div>
        
       
    <script>
        // Navigation dans le menu latéral
          document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function() {
                const section = this.getAttribute('data-section');
                window.location.href = `home.php?section=${section}`;
            });
        });
        
        // Effet de survol pour les cartes du tableau de bord
        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            });
        });
        
        // Initialisation du graphique
        document.addEventListener('DOMContentLoaded', function() {
            // Détecter le thème actuel
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const textColor = currentTheme === 'dark' ? '#f5f5f5' : '#333';
            
            const ctx = document.getElementById('gradesChart').getContext('2d');
            const gradesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Classe 1A', 'Classe 2B', 'Classe 3C', 'Classe 4D', 'Classe 5E'],
                    datasets: [{
                        label: 'Moyenne par classe',
                        data: [13.5, 14.8, 12.7, 15.2, 14.9],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(241, 196, 15, 0.7)',
                            'rgba(231, 76, 60, 0.7)'
                        ],
                        borderColor: [
                            'rgba(52, 152, 219, 1)',
                            'rgba(46, 204, 113, 1)',
                            'rgba(155, 89, 182, 1)',
                            'rgba(241, 196, 15, 1)',
                            'rgba(231, 76, 60, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 10,
                            max: 20,
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: currentTheme === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: currentTheme === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                            labels: {
                                color: textColor
                            }
                        },
                        title: {
                            color: textColor
                        }
                    }
                }
            });
            
            // Mettre à jour le graphique lors du changement de thème
            document.querySelector('.theme-toggle').addEventListener('click', function() {
                // Ajouter une classe pour la transition
                document.body.classList.add('theme-transition');
                
                // Supprimer la classe après la transition
                setTimeout(() => {
                    document.body.classList.remove('theme-transition');
                }, 500);
            });
        });
    </script>
</body>
</html>
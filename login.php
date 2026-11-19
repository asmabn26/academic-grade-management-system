<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Notes</title>
    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Ajout de la police Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --success-color: #047857;
            --light-color: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --text-color: #333;
            --muted-text: #4b5563;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(-45deg, #e6e9f0, #d1d5e0, #c3daff, #e6e9f0);
            background-size: 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: var(--text-color);
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
        .login-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            width: 400px;
            max-width: 90%;
            animation: fadeInUp 1s ease-out;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .login-header h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .login-header p {
            color: var(--muted-text);
            font-size: 1rem;
            font-weight: 400;
        }
        .login-form .form-group {
            margin-bottom: 15px;
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
            animation-delay: 0.5s;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        .login-form label {
            display: block;
            margin-bottom: 5px;
            color: var(--muted-text);
            font-weight: 500;
            font-size: 14px;
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: border-color 0.3s, transform 0.3s;
        }
        .login-form input:focus {
            border-color: var(--primary-color);
            outline: none;
            transform: scale(1.02);
        }
        .login-form button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 10px;
        }
        .login-form button:hover {
            background-color: #2563eb;
            transform: scale(1.05);
        }
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
            display: none;
        }
        .password-field {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted-text);
            font-size: 16px;
        }
        .user-type-options {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .user-type-option {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s;
            margin: 0 5px;
        }
        .user-type-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
        }
        .user-type-option.active {
            border-color: var(--primary-color);
            background-color: rgba(30, 58, 138, 0.1);
        }
        .user-type-option i {
            font-size: 24px;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        .user-type-option span {
            display: block;
            font-weight: 600;
            color: var(--muted-text);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Gestion des Notes</h1>
            <p id="login-subtitle">Connexion</p>
        </div>
        
        <form class="login-form" id="loginForm" action="verify.php" method="POST">
            <!-- Options de type d'utilisateur -->
            <div class="user-type-options">
                <div class="user-type-option active" data-type="ES" id="option-enseignant">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Enseignant (ES)</span>
                </div>
                <div class="user-type-option" data-type="SV" id="option-surveillant">
                    <i class="fas fa-user-shield"></i>
                    <span>Surveillant (SV)</span>
                </div>
            </div>
            
            <!-- Champ caché pour stocker le type d'utilisateur -->
            <input type="hidden" id="user-type" name="user-type" value="ES">
            
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            
            <div class="form-group">
                <label for="ne" id="ne-label">Numéro d'enseignant (NE)</label>
                <input type="text" id="ne" name="ne" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <button type="submit">Se connecter</button>
            
            <?php if(isset($_GET['error'])): ?>
            <div class="error-message" style="display: block;">
                <?php 
                $error_type = $_GET['error'];
                switch($error_type) {
                    case 'empty_fields':
                        echo "Tous les champs sont obligatoires.";
                        break;
                    case 'nom_incorrect':
                        echo "Le nom que vous avez saisi n'existe pas dans notre base de données.";
                        break;
                    case 'prenom_incorrect':
                        echo "Le prénom que vous avez saisi ne correspond pas au nom fourni.";
                        break;
                    case 'ne_incorrect':
                        echo "Le numéro que vous avez saisi est incorrect.";
                        break;
                    case 'password_incorrect':
                        echo "Le mot de passe que vous avez saisi est incorrect.";
                        break;
                    case 'db_error':
                        echo "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
                        break;
                    default:
                        echo "Identifiants incorrects. Veuillez réessayer.";
                }
                ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
    
    <script>
        // Gestion du choix du type d'utilisateur
        const optionEnseignant = document.getElementById('option-enseignant');
        const optionSurveillant = document.getElementById('option-surveillant');
        const userTypeInput = document.getElementById('user-type');
        const neLabel = document.getElementById('ne-label');
        const loginSubtitle = document.getElementById('login-subtitle');
        
        optionEnseignant.addEventListener('click', function() {
            setUserType('ES');
        });
        
        optionSurveillant.addEventListener('click', function() {
            setUserType('SV');
        });
        
        function setUserType(type) {
            userTypeInput.value = type;
            if (type === 'ES') {
                optionEnseignant.classList.add('active');
                optionSurveillant.classList.remove('active');
                neLabel.textContent = "Numéro d'enseignant (NE)";
                loginSubtitle.textContent = "Connexion Enseignant";
            } else {
                optionEnseignant.classList.remove('active');
                optionSurveillant.classList.add('active');
                neLabel.textContent = "Numéro de surveillant (SV)";
                loginSubtitle.textContent = "Connexion Surveillant";
            }
        }
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const nom = document.getElementById('nom').value.trim();
            const prenom = document.getElementById('prenom').value.trim();
            const ne = document.getElementById('ne').value.trim();
            const password = document.getElementById('password').value;
            
            if (!nom || !prenom || !ne || !password) {
                e.preventDefault();
                const errorMessage = document.querySelector('.error-message');
                errorMessage.textContent = 'Tous les champs sont obligatoires.';
                errorMessage.style.display = 'block';
            }
        });
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
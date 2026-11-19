<?php
// Démarrer la session
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $ne = trim($_POST['ne']);
    $password = $_POST['password'];
    $user_type = isset($_POST['user-type']) ? $_POST['user-type'] : 'ES'; // Récupérer le type d'utilisateur (ES ou SV)
    
    // Validation basique côté serveur
    if (empty($nom) || empty($prenom) || empty($ne) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }
    
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $db_password = "root"; // Mot de passe vide
    $dbname = "login";
    $port = 3307; // Port MySQL
    
    try {
        // Définir la chaîne DSN pour la connexion PDO
        $dsn = "mysql:host=$servername;dbname=$dbname;port=$port";
        // Utiliser $db_password au lieu de $password pour la connexion à la base de données
        $conn = new PDO($dsn, $username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Déterminer la table à utiliser en fonction du type d'utilisateur
        $table = ($user_type === 'ES') ? 'enseignants' : 'surveillant';
        
        // Requête préparée pour éviter les injections SQL
        $stmt = $conn->prepare("SELECT * FROM $table WHERE nom = :nom AND prenom = :prenom AND ne = :ne");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':ne', $ne);
        $stmt->execute();
        
        // Vérifier si l'utilisateur existe
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Hacher le mot de passe entré et le comparer avec celui stocké
            $hashed_password = hash('sha256', $password);
            
            if ($hashed_password === $user['mot_de_passe']) {
                // Authentification réussie
                $_SESSION['user_id'] = $user['idp'];
                error_log("Session définie : user_id = " . $user['idp'], 3, __DIR__ . '/debug.log');
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['ne'] = $user['ne'];
                $_SESSION['authenticated'] = true;
                $_SESSION['user_type'] = $user_type; // Stocker le type d'utilisateur dans la session
                
                // Rediriger vers la page appropriée en fonction du type d'utilisateur
                if ($user_type === 'ES') {
                    header("Location: home.php");
                } else {
                    header("Location: dashboard_secretaire.php");
                }
                exit();
            } else {
                // Mot de passe incorrect
                header("Location: login.php?error=password_incorrect");
                exit();
            }
        } else {
            // Vérifier si le nom existe
            $stmt = $conn->prepare("SELECT * FROM $table WHERE nom = :nom");
            $stmt->bindParam(':nom', $nom);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Le nom existe, vérifier si le prénom existe aussi
                $stmt = $conn->prepare("SELECT * FROM $table WHERE nom = :nom AND prenom = :prenom");
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    // Le nom et le prénom existent, donc c'est le NE qui est incorrect
                    header("Location: login.php?error=ne_incorrect");
                    exit();
                } else {
                    // Le nom existe mais pas le prénom
                    header("Location: login.php?error=prenom_incorrect");
                    exit();
                }
            } else {
                // Le nom n'existe pas
                header("Location: login.php?error=nom_incorrect");
                exit();
            }
        }
    } catch(PDOException $e) {
        // Erreur de connexion à la base de données
        header("Location: login.php?error=db_error");
        exit();
    }
    
} else {
    // Si quelqu'un tente d'accéder directement à ce fichier
    header("Location: login.php");
    exit();
}
?>
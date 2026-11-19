<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Étudiant - Gestion de Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --primary-hover: #1e3a8a;
            --secondary-color: #f8f9fa;
            --accent-color: #4cc9f0;
            --text-color: #333;
            --light-gray: #f0f2f5;
            --border-radius: 10px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: var(--text-color);
            padding-bottom: 40px;
        }
        
        .container-fluid {
            max-width: 1400px;
            padding: 0 24px;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 25px;
        }
        
        .card-body {
            padding: 30px 25px;
        }
        
        .page-header {
            background-color: #fff;
            padding: 20px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        h1, h5 {
            font-weight: 600;
            color: #2d3748;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: var(--primary-hover);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-size: 0.9rem;
        }
        
        .required-field::after {
            content: " *";
            color: #e53e3e;
            font-weight: bold;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 16px;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
            height: auto;
            font-size: 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.15);
        }
        
        .photo-preview {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #e2e8f0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        
        .photo-upload {
            width: 140px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #cbd5e0;
            cursor: pointer;
            transition: var(--transition);
            background-color: #f8fafc;
            padding: 10px;
            text-align: center;
            margin-top: 10px;
        }
        
        .photo-upload:hover {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .photo-upload i {
            font-size: 20px;
            color: #a0aec0;
            margin-right: 8px;
        }
        
        .photo-text {
            font-size: 14px;
            font-weight: 500;
            color: #718096;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-outline-secondary {
            color: #718096;
            border-color: #cbd5e0;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8fafc;
            color: #4a5568;
            border-color: #a0aec0;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 8px;
            margin-bottom: 24px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 40px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }
        
        .section-card {
            border-left: 4px solid var(--primary-color);
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            padding: 25px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: var(--box-shadow);
        }
        
        .photo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: 58px;
            padding: 1.5rem 0.75rem 0.5rem;
        }
        
        .form-floating > label {
            padding: 0.5rem 0.75rem;
            opacity: 0.65;
            font-size: 0.85rem;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }
        
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }
        
        .alert-info {
            background-color: rgba(67, 97, 238, 0.1);
            border-color: rgba(67, 97, 238, 0.2);
            color: var(--primary-color);
        }
        
        .form-section {
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            padding: 25px;
        }
        
        .form-section-header {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        
        .form-section-header i {
            margin-right: 10px;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .form-section-header h5 {
            margin-bottom: 0;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .top-bar {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 25px;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 25px;
        }
        
        .success-icon {
            font-size: 48px;
            color: #48bb78;
            margin-bottom: 20px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding: 0 16px;
            }
            
            .card-body {
                padding: 20px 15px;
            }
            
            .btn {
                padding: 8px 16px;
            }
            
            .form-section {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

$errors = [];
$firstName = $lastName = $CNE = $email = $phone = $birthDate = $gender = $address = $filiere = $niveau = $dateInscription = $photo = '';
$editId = $_GET['edit'] ?? null;
$student = [];

if ($editId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$editId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($student) {
            $firstName = $student['firstName'];
            $lastName = $student['lastName'];
            $CNE = $student['CNE'];
            $email = $student['email'];
            $phone = $student['phone'];
            $birthDate = $student['birthDate'];
            $gender = $student['gender'];
            $address = $student['address'];
            $filiere = $student['filiere'];
            $niveau = $student['niveau'];
            $dateInscription = $student['dateInscription'];
            $photo = $student['photo'] ? $student['photo'] : 'https://via.placeholder.com/150';
        } else {
            $errors[] = "Étudiant non trouvé.";
        }
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de la récupération de l'étudiant : " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $CNE = trim($_POST['CNE'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birthDate = $_POST['birthDate'] ?? null;
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $filiere = $_POST['filiere'] ?? 'GCDSTE';
    $niveau = $_POST['niveau'] ?? 'CI1';
    $dateInscription = $_POST['dateInscription'] ?? '';

    // Validation des champs obligatoires
    if (!$firstName) $errors[] = "Le prénom est obligatoire.";
    if (!$lastName) $errors[] = "Le nom est obligatoire.";
    if (!$CNE) $errors[] = "Le CNE est obligatoire.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email est invalide.";
   
    if (!$dateInscription) $errors[] = "La date d'inscription est obligatoire.";
    // Validation des dates
    if ($birthDate && !DateTime::createFromFormat('Y-m-d', $birthDate)) {
        $errors[] = "La date de naissance est invalide.";
    }
    if ($dateInscription && !DateTime::createFromFormat('Y-m-d', $dateInscription)) {
        $errors[] = "La date d'inscription est invalide.";
    }

    // Gestion de la photo
    $photo = $student['photo'] ?? 'https://via.placeholder.com/150';
    if (isset($_FILES['photoInput']) && $_FILES['photoInput']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photoInput'];
        if ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "La photo ne doit pas dépasser 2MB.";
        } elseif (!in_array($file['type'], ['image/jpeg', 'image/png'])) {
            $errors[] = "La photo doit être au format JPEG ou PNG.";
        } else {
            $uploadDir = 'Uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            // Supprimer l'ancienne photo si elle existe
            if ($student['photo'] && file_exists($student['photo']) && $student['photo'] !== 'https://via.placeholder.com/150') {
                unlink($student['photo']);
            }
            $photo = $uploadDir . uniqid() . '-' . basename($file['name']);
            if (!move_uploaded_file($file['tmp_name'], $photo)) {
                $errors[] = "Échec du téléchargement de la photo.";
            }
        }
    }

    if (empty($errors)) {
        try {
            if ($editId) {
                $stmt = $pdo->prepare("UPDATE students SET firstName = ?, lastName = ?, CNE = ?, email = ?, phone = ?, birthDate = ?, gender = ?, address = ?, filiere = ?, niveau = ?, dateInscription = ?, photo = ? WHERE id = ?");
                $stmt->execute([$firstName, $lastName, $CNE, $email, $phone, $birthDate ?: null, $gender, $address, $filiere, $niveau, $dateInscription, $photo, $editId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO students (firstName, lastName, CNE, email, phone, birthDate, gender, address, filiere, niveau, dateInscription, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $CNE, $email, $phone, $birthDate ?: null, $gender, $address, $filiere, $niveau, $dateInscription, $photo]);
            }
            header("Location: home.php?section=gestion_notes");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}
?>

    <div class="top-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3 mb-0">
                        <i class="bi <?php echo $editId ? 'bi-pencil-square' : 'bi-person-plus'; ?> text-primary"></i>
                        <?php echo $editId ? 'Modifier ' . htmlspecialchars($student['lastName'] . ' ' . $student['firstName']) : 'Ajouter un Étudiant'; ?>
                    </h1>
                </div>
                <div class="col-auto">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="home.php?section=dashboard"><i class="bi bi-house-door"></i> Tableau de bord</a></li>
                            <li class="breadcrumb-item"><a href="home.php?section=gestion_notes"><i class="bi bi-people"></i> Étudiants</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $editId ? 'Modifier' : 'Ajouter'; ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <class="row justify-content-center">
            <div class="col-lg-10">
                <form method="POST" action="ajouter_etudiant.php<?php echo $editId ? '?edit=' . $editId : ''; ?>" enctype="multipart/form-data" class="animate-fade-in">
                    <!-- Section Informations Personnelles -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="bi bi-person-badge"></i>
                            <h5>Informations Personnelles</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="photo-container mb-4">
                                    <img id="photoPreview" class="photo-preview" alt="Aperçu photo" src="<?php echo htmlspecialchars($photo); ?>" style="display: <?php echo $photo !== 'https://via.placeholder.com/150' ? 'block' : 'none'; ?>;">
                                    <div class="photo-upload" id="photoUploadBtn">
                                        <i class="bi bi-camera"></i>
                                        <span class="photo-text"><?php echo $photo !== 'https://via.placeholder.com/150' ? 'Modifier la photo' : 'Ajouter une photo'; ?></span>
                                    </div>
                                    <input type="file" id="photoInput" name="photoInput" accept="image/*" style="display: none;" onchange="previewPhoto(event)">
                                    <small class="d-block text-muted mt-2 text-center">JPEG ou PNG, max 2MB</small>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label required-field">Nom</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label required-field">Prénom</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="CNE" class="form-label required-field">CNE</label>
                                        <input type="text" class="form-control" id="CNE" name="CNE" value="<?php echo htmlspecialchars($CNE); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label required-field">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Informations Complémentaires -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="bi bi-info-circle"></i>
                            <h5>Informations Complémentaires</h5>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="birthDate" class="form-label">Date de naissance</label>
                                <input type="date" class="form-control" id="birthDate" name="birthDate" value="<?php echo $birthDate; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Genre</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" <?php echo empty($gender) ? 'selected' : ''; ?>>Sélectionnez un genre</option>
                                    <option value="M" <?php echo $gender === 'M' ? 'selected' : ''; ?>>Masculin</option>
                                    <option value="F" <?php echo $gender === 'F' ? 'selected' : ''; ?>>Féminin</option>
                                    <option value="Autre" <?php echo $gender === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Adresse</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
                            </div>
                             <div class="col-md-6">
                                <label for="dateInscription" class="form-label required-field">Date d'inscription</label>
                                <input type="date" class="form-control" id="dateInscription" name="dateInscription" value="<?php echo $dateInscription ?: date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Les champs marqués d'un <strong>*</strong> sont obligatoires.
                        </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="home.php?section=gestion_notes.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi <?php echo $editId ? 'bi-check-lg' : 'bi-save'; ?>"></i>
                            <?php echo $editId ? 'Mettre à jour' : 'Enregistrer l\'étudiant'; ?>
                        </button>
                    </div>
                </form>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mt-4" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewPhoto(event) {
            const input = event.target;
            const preview = document.getElementById('photoPreview');
            const uploadBtn = document.getElementById('photoUploadBtn');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert('La photo ne doit pas dépasser 2MB.');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    uploadBtn.querySelector('.photo-text').textContent = 'Modifier la photo';
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('photoUploadBtn').addEventListener('click', function() {
            document.getElementById('photoInput').click();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInscription = document.getElementById('dateInscription');
            if (!dateInscription.value) dateInscription.value = today;

            setTimeout(() => {
                document.querySelectorAll('.form-section').forEach((section, index) => {
                    section.style.opacity = '0';
                    section.style.transform = 'translateY(20px)';
                    section.style.animation = `fadeIn 0.5s ease ${index * 0.15}s forwards`;
                });
            }, 100);
        });
    </script>
</body>
</html>
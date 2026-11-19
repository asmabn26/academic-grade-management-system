<?php
// Ensure this file is only included within home.php
if (!defined('PHP_VERSION_ID')) {
    exit('Direct access not permitted');
}

session_start();

require_once 'config.php';



// Récupérer l'identifiant du professeur connecté
$teacherId = getCurrentTeacherId($pdo);
if (!$teacherId) {
    die("Erreur : Aucun professeur connecté.");
}

// Gérer la suppression
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Supprimer l'étudiant de la table students
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
    // Supprimer les notes associées dans la table notes
    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: home.php?section=gestion_notes");
    exit;
}

// Construire la requête SQL pour récupérer les étudiants et leurs notes
$sql = "SELECT 
    s.id,
    s.firstName,
    s.lastName,
    s.photo,
    s.CNE,
    n.noteExamen1,
    n.noteExamen2,
    n.noteExamen3
FROM 
    students s 
LEFT JOIN 
    notes n ON s.id = n.id 
    WHERE
    n.idp = ?";
;

$stmt = $pdo->prepare($sql);

$params = [$teacherId];

// Récupérer les étudiants avec leurs notes

$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nettoyer les données pour l'exportation
  $cleanedStudents = array_map(function($student) {
    return [
        'id' => isset($student['id']) ? (int) $student['id'] : null,
        'lastName' => isset($student['lastName']) ? $student['lastName'] : '',
        'firstName' => isset($student['firstName']) ? $student['firstName'] : '',
        'CNE' => isset($student['CNE']) ? $student['CNE'] : '',
        'photo' => isset($student['photo']) ? $student['photo'] : 'https://via.placeholder.com/40',
        'noteExamen1' => isset($student['noteExamen1']) ? (float) $student['noteExamen1'] : null,
        'noteExamen2' => isset($student['noteExamen2']) ? (float) $student['noteExamen2'] : null,
        'noteExamen3' => isset($student['noteExamen3']) ? (float) $student['noteExamen3'] : null,
        'filiere' => isset($student['filiere']) ? $student['filiere'] : '',
        'niveau' => isset($student['niveau']) ? $student['niveau'] : '',
        'email' => isset($student['email']) ? $student['email'] : '',
        'phone' => isset($student['phone']) ? $student['phone'] : '',
        'birthDate' => isset($student['birthDate']) ? $student['birthDate'] : '',
        'gender' => isset($student['gender']) ? $student['gender'] : '',
        'address' => isset($student['address']) ? $student['address'] : '',
        'dateInscription' => isset($student['dateInscription']) ? $student['dateInscription'] : ''
    ];
}, $students);


// Gérer les notes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveNotes'])) {
    foreach ($_POST['notes'] as $studentId => $exams) {
        // Vérifier si une entrée existe déjà pour cet étudiant et ce professeur
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE id = ? AND idp = ?");
        $stmt->execute([$studentId, $teacherId]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            // Mettre à jour l'entrée existante
            $stmt = $pdo->prepare("UPDATE notes SET noteExamen1 = ?, noteExamen2 = ?, noteExamen3 = ? WHERE id = ? AND idp = ?");
            $stmt->execute([
                $exams[1] !== '' ? floatval($exams[1]) : null,
                $exams[2] !== '' ? floatval($exams[2]) : null,
                $exams[3] !== '' ? floatval($exams[3]) : null,
                $studentId,
                $teacherId
            ]);
        } else {
            // Insérer une nouvelle entrée
            $stmt = $pdo->prepare("INSERT INTO notes (id, idp, noteExamen1, noteExamen2, noteExamen3) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $studentId,
                $teacherId,
                $exams[1] !== '' ? floatval($exams[1]) : null,
                $exams[2] !== '' ? floatval($exams[2]) : null,
                $exams[3] !== '' ? floatval($exams[3]) : null
            ]);
        }
    }
       
    header("Location: home.php?section=gestion_notes&saved=1" . ($filiereFilter || $niveauFilter ? "&filiere=" . urlencode($filiereFilter) . "&niveau=" . urlencode($niveauFilter) : ""));
    exit;
}
?>

<!-- Dépendances CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<!-- Styles spécifiques à la section -->
<style>
    .gestion-notes-container {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7fa;
        color: #212529;
    }
    .gestion-notes-container .container-fluid {
        padding: 0 2rem;
    }
    .gestion-notes-container .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .gestion-notes-container .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem 1.5rem;
    }
    .gestion-notes-container .form-control,
    .gestion-notes-container .form-select {
        border-color: #dee2e6;
    }
    .gestion-notes-container .form-control:focus,
    .gestion-notes-container .form-select:focus {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
    }
    .gestion-notes-container .btn-primary {
        background-color: #1e3a8a;
        border-color: #1e3a8a;
    }
    .gestion-notes-container .btn-primary:hover {
        background-color: #1e3a8a;
        border-color: #1e3a8a;
    }
    .gestion-notes-container .page-title {
        font-weight: 600;
        color: #212529;
        margin-top: 20px;
    }
    .gestion-notes-container .page-title i {
        color: #1e3a8a;
        margin-right: 10px;
    }
    .gestion-notes-container .table {
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }
    .gestion-notes-container .table th,
    .gestion-notes-container .table td {
        border: none;
        vertical-align: middle;
        padding: 0.75rem 1rem;
    }
    .gestion-notes-container .table tbody tr {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .gestion-notes-container .table tbody tr:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .gestion-notes-container .student-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #dee2e6;
    }
    .gestion-notes-container .note-input {
        width: 80px;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        font-size: 0.9rem;
    }
    .gestion-notes-container .note-input:focus {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
    }
    .gestion-notes-container .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    .gestion-notes-container .action-buttons .btn {
        padding: 0.25rem 0.75rem;
    }
    .gestion-notes-container .modal-content {
        border-radius: 8px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .gestion-notes-container .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .gestion-notes-container .modal-body {
        padding: 1.5rem;
    }
    .gestion-notes-container .modal-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #dee2e6;
        margin-bottom: 1rem;
    }
    .gestion-notes-container .detail-item {
        margin-bottom: 0.75rem;
    }
    .gestion-notes-container .detail-item strong {
        color: #1e3a8a;
    }
    .gestion-notes-container .filter-group {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    .gestion-notes-container .filter-group .form-control,
    .gestion-notes-container .filter-group .form-select {
        max-width: 200px;
    }
    .gestion-notes-container .export-btn {
        background-color: #4bb543;
        border-color: #4bb543;
    }
    .gestion-notes-container .export-btn:hover {
        background-color: #3d9c35;
        border-color: #3d9c35;
    }
    @media (max-width: 768px) {
        .gestion-notes-container .container-fluid {
            padding: 0 1rem;
        }
        .gestion-notes-container .table-responsive {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .gestion-notes-container .action-buttons {
            flex-direction: column;
            gap: 0.25rem;
        }
        .gestion-notes-container .filter-group {
            flex-direction: column;
            align-items: stretch;
        }
        .gestion-notes-container .filter-group .form-control,
        .gestion-notes-container .filter-group .form-select {
            max-width: none;
        }
    }
</style>

<?php if (isset($_GET['added'])): ?>
<div class="gestion-notes-container">
    <div class="container-fluid py-4">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Étudiant ajouté avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="gestion-notes-container">
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="bi bi-people-fill"></i>
                    Liste des Étudiants GCDSTE-1
                </h1>
               
            </div>
            <div class="col-auto">
                <a href="ajouter_etudiant.php" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Ajouter un étudiant
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Gestion des Étudiants</h5>
                    </div>
            
                </div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">N°</th>
                                    <th scope="col">Photo</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">CNE</th>
                                    <th scope="col">Note Examen 1</th>
                                    <th scope="col">Note Examen 2</th>
                                    <th scope="col">Note Examen 3</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                <?php if (empty($students)): ?>
                                    <tr><td colspan="8" class="text-center py-4">Aucun étudiant trouvé</td></tr>
                                <?php else: ?>
                                    <?php foreach ($students as $index => $student): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><img src="<?php echo htmlspecialchars( $student['photo'] ?? ''); ?>"  alt="<?php echo htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?>" class="student-photo"></td>
                                            <td><strong><?php echo htmlspecialchars($student['lastName'] . ' ' . $student['firstName']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($student['CNE']); ?></td>
                                            <td>
                                                <input type="number" class="note-input" name="notes[<?php echo $student['id']; ?>][1]" value="<?php echo $student['noteExamen1'] !== null ? number_format($student['noteExamen1'], 2) : ''; ?>" min="0" max="20" step="0.1">
                                            </td>
                                            <td><input type="number" class="note-input" name="notes[<?php echo $student['id']; ?>][2]" value="<?php echo $student['noteExamen2'] !== null ? number_format($student['noteExamen2'], 2) : ''; ?>" min="0" max="20" step="0.1"></td>
                                            <td><input type="number" class="note-input" name="notes[<?php echo $student['id']; ?>][3]" value="<?php echo $student['noteExamen3'] !== null ? number_format($student['noteExamen3'], 2) : ''; ?>" min="0" max="20" step="0.1"></td>
                                            <td class="action-buttons">
                                                <a href="ajouter_etudiant.php?edit=<?php echo $student['id'] ; ?>" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="home.php?section=gestion_notes&delete=<?php echo $student['id'] ; ?>" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                       
                        <button type="submit" class="btn btn-primary" name="saveNotes">
                            <i class="bi bi-save"></i> Enregistrer les notes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails Étudiant -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-labelledby="studentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailModalLabel"><i class="bi bi-person-circle me-2"></i> Détails de l'Étudiant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="https://via.placeholder.com/150" class="modal-photo" id="modalStudentPhoto" alt="Photo étudiant">
                    <h4 id="modalStudentName" class="mt-2"></h4>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="detail-item"><strong>CNE :</strong> <span id="modalStudentCNE"></span></div>
                        <div class="detail-item"><strong>Email :</strong> <span id="modalStudentEmail"></span></div>
                        <div class="detail-item"><strong>Téléphone :</strong> <span id="modalStudentPhone"></span></div>
                        <div class="detail-item"><strong>Date de naissance :</strong> <span id="modalStudentBirthDate"></span></div>
                        <div class="detail-item"><strong>Genre :</strong> <span id="modalStudentGender"></span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item"><strong>Adresse :</strong> <span id="modalStudentAddress"></span></div>
                        <div class="detail-item"><strong>Filière :</strong> <span id="modalStudentFiliere"></span></div>
                        <div class="detail-item"><strong>Niveau :</strong> <span id="modalStudentNiveau"></span></div>
                        <div class="detail-item"><strong>Date d'inscription :</strong> <span id="modalStudentDateInscription"></span></div>
                    </div>
                </div>
                <div class="mt-4">
                    <h6>Notes</h6>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Examen 1 :</strong> <span id="modalStudentNote1">-</span></li>
                        <li class="list-group-item"><strong>Examen 2 :</strong> <span id="modalStudentNote2">-</span></li>
                        <li class="list-group-item"><strong>Examen 3 :</strong> <span id="modalStudentNote3">-</span></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Dépendances JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Notification pour les notes sauvegardées
<?php if (isset($_GET['saved'])): ?>
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 p-3';
    toast.style.zIndex = '11';
    toast.innerHTML = `
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Succès</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Notes enregistrées avec succès.
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
<?php endif; ?>


// Gestion du modal de détails
document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', function() {
        const studentId = this.getAttribute('data-id');
        const student = students.find(s => s.id == studentId);

        if (student) {
            document.getElementById('modalStudentPhoto').src = student.photo;
            document.getElementById('modalStudentName').textContent = `${student.lastName} ${student.firstName}`;
            document.getElementById('modalStudentCNE').textContent = student.CNE;
            document.getElementById('modalStudentEmail').textContent = student.email || '-';
            document.getElementById('modalStudentPhone').textContent = student.phone || '-';
            document.getElementById('modalStudentBirthDate').textContent = student.birthDate || '-';
            document.getElementById('modalStudentGender').textContent = student.gender || '-';
            document.getElementById('modalStudentAddress').textContent = student.address || '-';
            document.getElementById('modalStudentFiliere').textContent = student.filiere;
            document.getElementById('modalStudentNiveau').textContent = student.niveau;
            document.getElementById('modalStudentDateInscription').textContent = student.dateInscription || '-';
            document.getElementById('modalStudentNote1').textContent = student.noteExam1 !== null ? student.noteExam1.toFixed(2) : '-';
            document.getElementById('modalStudentNote2').textContent = student.noteExam2 !== null ? student.noteExam2.toFixed(2) : '-';
            document.getElementById('modalStudentNote3').textContent = student.noteExam3 !== null ? student.noteExam3.toFixed(2) : '-';

            const modal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
            modal.show();
        }
    });
});

// Filtres côté client
const searchInput = document.getElementById('searchInput');
const filiereFilter = document.getElementById('filiereFilter');
const tableBody = document.getElementById('studentTableBody');

function filterStudents() {
    const searchTerm = searchInput.value.toLowerCase();
    let filiere = initialFiliere;
    

    if (filiereFilter) {
        filiere = filiereFilter.value;
        
    }

    const filteredStudents = students.filter(student => {
        const fullName = `${student.lastName} ${student.firstName}`.toLowerCase();
        return fullName.includes(searchTerm) &&
               (filiere === '' || student.filiere === filiere) 
              ;
    });

    tableBody.innerHTML = filteredStudents.length === 0
        ? '<tr><td colspan="8" class="text-center py-4">Aucun étudiant trouvé</td></tr>'
        : filteredStudents.map((student, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><img src="${student.photo}" alt="${student.firstName} ${student.lastName}" class="student-photo"></td>
                <td><strong>${student.lastName} ${student.firstName}</strong></td>
                <td><span class="text-muted">${student.CNE}</span></td>
                <td><input type="number" class="note-input" name="notes[${student.id}][1]" value="${student.noteExam1 !== null ? student.noteExam1.toFixed(2) : ''}" min="0" max="20" step="0.1"></td>
                <td><input type="number" class="note-input" name="notes[${student.id}][2]" value="${student.noteExam2 !== null ? student.noteExam2.toFixed(2) : ''}" min="0" max="20" step="0.1"></td>
                <td><input type="number" class="note-input" name="notes[${student.id}][3]" value="${student.noteExam3 !== null ? student.noteExam3.toFixed(2) : ''}" min="0" max="20" step="0.1"></td>
                <td class="action-buttons">
                    <a href="ajouter_etudiant.php?edit=${student.id}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="home.php?section=gestion_notes&delete=${student.id}${filiereFilter || niveauFilter ? '&filiere=' + encodeURIComponent(filiereFilter) + '&niveau=' + encodeURIComponent(niveauFilter) : ''}" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
        `).join('');
}

searchInput.addEventListener('input', filterStudents);
if (filiereFilter && niveauFilter) {
    filiereFilter.addEventListener('change', filterStudents);
   
}

// Exportation vers Excel
document.getElementById('exportBtn').addEventListener('click', () => {
    try {
        if (typeof XLSX === 'undefined') {
            throw new Error('SheetJS n\'est pas chargé.');
        }

        const exportData = students.map((student, index) => ({
            '#': index + 1,
            'Nom Complet': `${student.lastName || ''} ${student.firstName || ''}`.trim(),
            'CNE': student.CNE || '',
            'Note Exam 1': student.noteExamen1 != null && !isNaN(student.noteExamen1) ? Number(student.noteExamen1).toFixed(2) : '',
            'Note Exam 2': student.noteExamen2 != null && !isNaN(student.noteExamen2) ? Number(student.noteExamen2).toFixed(2) : '',
            'Note Exam 3': student.noteExamen3 != null && !isNaN(student.noteExamen3) ? Number(student.noteExamen3).toFixed(2) : ''
        }));

        const worksheet = XLSX.utils.json_to_sheet(exportData);
        worksheet['!cols'] = [
            { wch: 5 },
            { wch: 20 },
            { wch: 12 },
            { wch: 10 },
            { wch: 10 },
            { wch: 10 }
        ];

        const range = XLSX.utils.decode_range(worksheet['!ref']);
        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const cell_address = { c: C, r: R };
                const cell_ref = XLSX.utils.encode_cell(cell_address);
                if (!worksheet[cell_ref]) continue;
                worksheet[cell_ref].s = {
                    border: {
                        top: { style: 'thin', color: { auto: 1 } },
                        right: { style: 'thin', color: { auto: 1 } },
                        bottom: { style: 'thin', color: { auto: 1 } },
                        left: { style: 'thin', color: { auto: 1 } }
                    }
                };
            }
        }

        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Etudiants');
        XLSX.writeFile(workbook, 'etudiants.xlsx');

        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '11';
        toast.innerHTML = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Succès</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Tableau exporté vers Excel avec succès.
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    } catch (error) {
        console.error('Erreur lors de l\'exportation :', error);
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '11';
        toast.innerHTML = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Erreur</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Une erreur s'est produite lors de l'exportation : ${error.message}
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
});

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    filterStudents();
});
</script>
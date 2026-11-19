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
// Récupérer l'emploi du temps du professeur depuis la base de données
try {
$stmt = $pdo->prepare("SELECT schedule_data FROM emploie WHERE teacher_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$teacherId]);
$schedule = $stmt->fetch(PDO::FETCH_ASSOC);

// Valider et encoder les données
    if ($schedule && !empty($schedule['schedule_data'])) {
        // Vérifier que schedule_data est un JSON valide
        $decodedData = json_decode($schedule['schedule_data'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
            $initialData = json_encode($decodedData);
        } else {
            $initialData = '[]';
            error_log("Erreur JSON dans schedule_data pour teacher_id $teacherId : " . json_last_error_msg());
        }
    } else {
        $initialData = '[]';
    }
}
catch (Exception $e) {
    $initialData = '[]';
    error_log("Erreur lors de la récupération de l'emploi du temps pour teacher_id $teacherId : " . $e->getMessage());
}
?>

<style>
    table {
        border-collapse: collapse;
        width: 100%;
        max-width: 800px;
        margin: 20px auto;
        font-family: Arial, sans-serif;
        margin-top: 100px;
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
</style>

<div class="dashboard-card">
    <h3><i class="fas fa-clock"></i> Emploi du temps</h3>
     <p>Votre emploi du temps pour le deuxième semestre.</p>
    <p>Année universitaire: 2024-2025</p>
    <div id="tableContainer"></div>
    <div id="errorMessage" class="error"></div>
    <div class="file-input-container">
        <input type="file" id="excelFile" accept=".xlsx, .xls" onchange="loadExcelFile()">
        <label for="excelFile">Importer votre emploi du temps(fichier-excel)</label>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
  function displayTable(jsonData) {
        if (!jsonData || jsonData.length === 0) {
            document.getElementById('errorMessage').innerHTML = 'Aucune donnée à afficher.';
            return;
        }

        const headers = jsonData[0];
        if (!headers.includes('lundi') && !headers.some(h => h.includes('8:30-10:30'))) {
            document.getElementById('errorMessage').innerHTML = 'Les en-têtes ne contiennent pas les colonnes attendues (ex. "lundi", "8:30-10:30"). Vérifiez votre fichier Excel.';
            return;
        }

        let table = '<table>';
        jsonData.forEach((row, rowIndex) => {
            table += '<tr>';
            row.forEach(cell => {
                const cellContent = cell == null ? '' : cell;
                table += rowIndex === 0 ? `<th>${cellContent}</th>` : `<td>${cellContent}</td>`;
            });
            table += '</tr>';
        });
        table += '</table>';

        document.getElementById('tableContainer').innerHTML = table;
    }

    async function loadExcelFile() {
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.innerHTML = '';

        const fileInput = document.getElementById('excelFile');
        const file = fileInput.files[0];

        if (!file) {
            errorMessage.innerHTML = 'Veuillez sélectionner un fichier Excel.';
            return;
        }

        try {
            const reader = new FileReader();
            reader.onload = async function(e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array', cellDates: true });

                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];

                const range = XLSX.utils.decode_range(worksheet['!ref']);
                const jsonData = [];

                for (let rowNum = range.s.r; rowNum <= range.e.r; rowNum++) {
                    const row = [];
                    for (let colNum = range.s.c; colNum <= range.e.c; colNum++) {
                        const cellAddress = XLSX.utils.encode_cell({ r: rowNum, c: colNum });
                        const cell = worksheet[cellAddress];
                        row.push(cell ? cell.v : '');
                    }
                    jsonData.push(row);
                }

                if (jsonData.length === 0) {
                    errorMessage.innerHTML = 'Le fichier Excel est vide.';
                    return;
                }

                // Afficher les données dans le tableau
                displayTable(jsonData);

                // Envoyer les données au serveur
                try {
                    const response = await fetch('save_schedule.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ scheduleData: jsonData })
                    });

                    const result = await response.json();
                    if (!result.success) {
                        errorMessage.innerHTML = 'Erreur lors de l\'enregistrement de l\'emploi du temps : ' + result.message;
                    }
                } catch (error) {
                    errorMessage.innerHTML = 'Erreur lors de la communication avec le serveur : ' + error.message;
                }
            };

            reader.onerror = function() {
                errorMessage.innerHTML = 'Erreur lors de la lecture du fichier Excel.';
            };

            reader.readAsArrayBuffer(file);

        } catch (error) {
            errorMessage.innerHTML = 'Erreur lors de la lecture du fichier Excel : ' + error.message;
        }
    }

    // Charger les données initiales depuis le serveur
    window.addEventListener('load', function() {
        try {
            const initialData = <?php echo $initialData; ?>;
            console.log('Données initiales chargées : ', initialData);
            if (initialData && initialData.length > 0) {
                displayTable(initialData);
            } else {
                document.getElementById('errorMessage').innerHTML = 'Aucun emploi du temps disponible. Veuillez charger un fichier Excel.';
                console.warn('Aucune donnée initiale disponible.');
            }
        } catch (error) {
            document.getElementById('errorMessage').innerHTML = 'Erreur lors du chargement des données initiales.';
            console.error('Erreur lors du chargement des données initiales : ', error.message);
        }
    });
</script>
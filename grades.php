<?php
// Ensure this file is only included within home.php
if (!defined('PHP_VERSION_ID')) {
    exit('Direct access not permitted');
}
?>

<div class="welcome-card">
    <h2>Gestion des notes</h2>
    <p>Saisissez et modifiez les notes de vos étudiants.</p>
</div>

<div class="dashboard-card">
    <h3><i class="fas fa-users"></i> Choisir une classe</h3>
    <p>Sélectionnez une classe pour saisir ou modifier les notes.</p>
    <select class="form-control" id="classSelect">
        <option>Classe 1A - Mathématiques</option>
        <option>Classe 2B - Physique</option>
        <option>Classe 3C - Chimie</option>
        <option>Classe 4D - Biologie</option>
        <option>Classe 5E - Informatique</option>
    </select>
    <a href="#" class="btn" style="margin-top: 15px;">Sélectionner</a>
</div>
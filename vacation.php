<?php
// Ensure this file is only included within home.php
if (!defined('PHP_VERSION_ID')) {
    exit('Direct access not permitted');
}
?>

<style>
    .welcome-card {
        text-align: center;
        width: 80%; /* Augmenter la largeur de la carte */
        max-width: 1000px; /* Largeur maximale pour éviter un étirement excessif */
        margin: 0 auto; /* Centrer la carte */
        padding: 20px;
    }
    .image-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
    }
    .image-container img {
        width: 1000px;
        height: 1000px;
        object-fit: contain;
    
        border-radius: 5px;
    }
</style>

<div class="welcome-card">
    <h2>Vacances scolaires</h2>
    <p>Consultez le calendrier des vacances scolaires.</p>
<?php
// Définir le chemin de l'image
$image_path = "uploads/vacances.jpg";

// Afficher l'image si elle existe
if (file_exists($image_path)) {
    echo '<div class="image-container">';
    echo '<img src="' . $image_path . '" alt="Vacances scolaires">';
    echo '</div>';
}
?>
</div>

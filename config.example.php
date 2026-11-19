<?php
// Copiez ce fichier vers config.php et utilisez uniquement des identifiants locaux.
$host = 'localhost';
$database = 'your_database_name';
$username = 'your_local_username';
$password = 'your_local_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    exit('Database connection failed.');
}

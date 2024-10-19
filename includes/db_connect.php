<?php
// Fichier: includes/db_connect.php

// Paramètres de connexion à la base de données
$host = 'mysql-itachi.alwaysdata.net';
$db   = 'itachi_md';
$user = 'itachi';
$pass = 'Anoman2002';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Connexion réussie";
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Fonction pour obtenir la connexion
function getDB() {
    global $pdo;
    return $pdo;
}
?>
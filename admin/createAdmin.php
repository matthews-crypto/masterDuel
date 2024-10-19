<?php
// Assurez-vous que la fonction createAdmin est définie ici ou incluse depuis un autre fichier
require_once '../includes/functions.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        if (createAdmin($username, $password)) {
            echo "Administrateur ajouté avec succès!";
        } else {
            echo "Erreur lors de l'ajout de l'administrateur.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un administrateur</title>
</head>
<body>
    <h2>Ajouter un nouvel administrateur</h2>
    <form method="POST" action="">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Ajouter l'administrateur">
    </form>
</body>
</html>
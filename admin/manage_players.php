<?php
// Fichier: admin/manage_players.php

session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $division_id = isset($_POST['division_id']) ? $_POST['division_id'] : null;
                if (addPlayer($_POST['pseudo'], $_POST['logo_image'], $_POST['main_deck'], $_POST['secondary_deck'], $division_id)) {
                    $message = "Joueur ajouté avec succès.";
                } else {
                    $message = "Erreur lors de l'ajout du joueur.";
                }
                break;
            case 'update':
                $division_id = isset($_POST['division_id']) ? $_POST['division_id'] : null;
                if (updatePlayer($_POST['id'], $_POST['pseudo'], $_POST['logo_image'], $_POST['main_deck'], $_POST['secondary_deck'], $division_id)) {
                    $message = "Joueur mis à jour avec succès.";
                } else {
                    $message = "Erreur lors de la mise à jour du joueur.";
                }
                break;
            case 'delete':
                if (deletePlayer($_POST['id'])) {
                    $message = "Joueur supprimé avec succès.";
                } else {
                    $message = "Erreur lors de la suppression du joueur.";
                }
                break;
        }
    }
}

$players = getAllPlayers();
$divisions = getDivisions();

// Si aucune division n'existe, initialisons-les
if (empty($divisions)) {
    initializeDivisions();
    $divisions = getDivisions();
    $message .= " Les divisions ont été initialisées.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Joueurs</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Gestion des Joueurs</h1>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Ajouter un joueur</h2>
    <form method="post">
        <input type="hidden" name="action" value="add">
        <label for="pseudo">Pseudo:</label>
        <input type="text" id="pseudo" name="pseudo" required>
        <label for="logo_image">Logo Image:</label>
        <input type="text" id="logo_image" name="logo_image">
        <label for="main_deck">Deck Principal:</label>
        <input type="text" id="main_deck" name="main_deck">
        <label for="secondary_deck">Deck Secondaire:</label>
        <input type="text" id="secondary_deck" name="secondary_deck">
        <label for="division_id">Division:</label>
        <select id="division_id" name="division_id">
            <?php foreach ($divisions as $division): ?>
                <option value="<?php echo $division['id']; ?>"><?php echo htmlspecialchars($division['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Ajouter</button>
    </form>

    <h2>Liste des joueurs</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Pseudo</th>
            <th>Logo</th>
            <th>Deck Principal</th>
            <th>Deck Secondaire</th>
            <th>Division</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($players as $player): ?>
            <tr>
                <td><?php echo $player['id']; ?></td>
                <td><?php echo htmlspecialchars($player['pseudo']); ?></td>
                <td><?php echo htmlspecialchars($player['logo_image']); ?></td>
                <td><?php echo htmlspecialchars($player['main_deck']); ?></td>
                <td><?php echo htmlspecialchars($player['secondary_deck']); ?></td>
                <td><?php echo htmlspecialchars(getDivisionName($player['division_id'])); ?></td>
                <td>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $player['id']; ?>">
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?');">Supprimer</button>
                    </form>
                    <button onclick="editPlayer(<?php echo htmlspecialchars(json_encode($player)); ?>)">Modifier</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div id="editForm" style="display: none;">
        <h2>Modifier un joueur</h2>
        <form method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_id" name="id">
            <label for="edit_pseudo">Pseudo:</label>
            <input type="text" id="edit_pseudo" name="pseudo" required>
            <label for="edit_logo_image">Logo Image:</label>
            <input type="text" id="edit_logo_image" name="logo_image">
            <label for="edit_main_deck">Deck Principal:</label>
            <input type="text" id="edit_main_deck" name="main_deck">
            <label for="edit_secondary_deck">Deck Secondaire:</label>
            <input type="text" id="edit_secondary_deck" name="secondary_deck">
            <label for="edit_division_id">Division:</label>
            <select id="edit_division_id" name="division_id">
                <?php foreach ($divisions as $division): ?>
                    <option value="<?php echo $division['id']; ?>"><?php echo htmlspecialchars($division['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Mettre à jour</button>
        </form>
    </div>

    <script>
        function editPlayer(player) {
            document.getElementById('edit_id').value = player.id;
            document.getElementById('edit_pseudo').value = player.pseudo;
            document.getElementById('edit_logo_image').value = player.logo_image;
            document.getElementById('edit_main_deck').value = player.main_deck;
            document.getElementById('edit_secondary_deck').value = player.secondary_deck;
            document.getElementById('edit_division_id').value = player.division_id;
            document.getElementById('editForm').style.display = 'block';
        }
    </script>
</body>
</html>
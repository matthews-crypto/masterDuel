<?php
// Fichier: admin/manage_matches.php

session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add_match') {
        if (addMatch($_POST['player_id'], $_POST['result'])) {
            $message = "Match ajouté avec succès.";
        } else {
            $message = "Erreur lors de l'ajout du match.";
        }
    }
}

$players = getAllPlayers();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Matchs</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Gestion des Matchs</h1>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Ajouter un match</h2>
    <form method="post">
        <input type="hidden" name="action" value="add_match">
        <label for="player_id">Joueur:</label>
        <select id="player_id" name="player_id" required>
            <?php foreach ($players as $player): ?>
                <option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['pseudo']); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="result">Résultat:</label>
        <select id="result" name="result" required>
            <option value="win">Victoire (+3 points)</option>
            <option value="draw">Match nul (+1 point)</option>
            <option value="loss">Défaite (+0 point)</option>
        </select>
        <button type="submit">Ajouter le match</button>
    </form>

    <h2>Historique des matchs par joueur</h2>
    <form id="player_history_form">
        <label for="history_player_id">Sélectionner un joueur:</label>
        <select id="history_player_id" name="history_player_id">
            <?php foreach ($players as $player): ?>
                <option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['pseudo']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="loadPlayerHistory()">Afficher l'historique</button>
    </form>
    <div id="player_history"></div>

    <script>
    function loadPlayerHistory() {
        var playerId = document.getElementById('history_player_id').value;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('player_history').innerHTML = this.responseText;
            }
        };
        xhr.open("GET", "get_player_history.php?player_id=" + playerId, true);
        xhr.send();
    }
    </script>
</body>
</html>
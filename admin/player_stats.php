<?php
// Fichier: admin/player_stats.php

session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdminLogin();

$players = getAllPlayers();
$divisions = getDivisions();
$leaderboard = getLeaderboard();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Joueurs</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Statistiques des Joueurs</h1>

    <h2>Classement Général</h2>
    <table>
        <tr>
            <th>Rang</th>
            <th>Pseudo</th>
            <th>Division</th>
            <th>Points</th>
            <th>Matchs Joués</th>
            <th>Victoires</th>
            <th>Taux de Victoire</th>
        </tr>
        <?php foreach ($leaderboard as $index => $player): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($player['pseudo']); ?></td>
                <td><?php echo htmlspecialchars($player['division_name']); ?></td>
                <td><?php echo $player['points']; ?></td>
                <td><?php echo $player['total_matches']; ?></td>
                <td><?php echo $player['wins']; ?></td>
                <td><?php echo number_format($player['win_rate'], 2); ?>%</td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Statistiques Détaillées par Joueur</h2>
    <form id="player_stats_form">
        <label for="player_id">Sélectionner un joueur:</label>
        <select id="player_id" name="player_id">
            <?php foreach ($players as $player): ?>
                <option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['pseudo']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="loadPlayerStats()">Afficher les statistiques</button>
    </form>
    <div id="player_stats"></div>

    <script>
    function loadPlayerStats() {
        var playerId = document.getElementById('player_id').value;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('player_stats').innerHTML = this.responseText;
            }
        };
        xhr.open("GET", "get_player_stats.php?player_id=" + playerId, true);
        xhr.send();
    }
    </script>
</body>
</html>
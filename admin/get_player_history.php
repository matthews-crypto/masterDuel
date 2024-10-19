<?php
// Fichier: admin/get_player_history.php

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdminLogin();

if (isset($_GET['player_id'])) {
    $player_id = $_GET['player_id'];
    $player = getPlayer($player_id);
    $matches = getPlayerMatches($player_id);
    $winRate = calculateWinRate($player_id);

    echo "<h3>Historique des matchs de " . htmlspecialchars($player['pseudo']) . "</h3>";
    echo "<p>Points totaux : " . $player['points'] . "</p>";
    echo "<p>Taux de victoire : " . number_format($winRate, 2) . "%</p>";
    echo "<table>";
    echo "<tr><th>Date</th><th>Résultat</th></tr>";
    foreach ($matches as $match) {
        echo "<tr>";
        echo "<td>" . $match['match_date'] . "</td>";
        echo "<td>" . ucfirst($match['result']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Aucun joueur sélectionné.";
}
?>
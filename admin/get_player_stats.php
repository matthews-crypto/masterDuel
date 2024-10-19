<?php
// Fichier: admin/get_player_stats.php

require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdminLogin();

if (isset($_GET['player_id'])) {
    $player_id = $_GET['player_id'];
    $player = getPlayer($player_id);
    $stats = getPlayerStats($player_id);
    $rank = getPlayerRank($player_id);

    echo "<h3>Statistiques de " . htmlspecialchars($player['pseudo']) . "</h3>";
    echo "<p>Rang actuel : " . $rank . "</p>";
    echo "<p>Division : " . htmlspecialchars(getDivisionName($player['division_id'])) . "</p>";
    echo "<p>Points totaux : " . $stats['points'] . "</p>";
    echo "<p>Matchs joués : " . $stats['total_matches'] . "</p>";
    echo "<p>Victoires : " . $stats['wins'] . "</p>";
    echo "<p>Matchs nuls : " . $stats['draws'] . "</p>";
    echo "<p>Défaites : " . $stats['losses'] . "</p>";
    echo "<p>Taux de victoire : " . number_format($stats['win_rate'], 2) . "%</p>";
} else {
    echo "Aucun joueur sélectionné.";
}

function getDivisionName($division_id) {
    $divisions = getDivisions();
    foreach ($divisions as $division) {
        if ($division['id'] == $division_id) {
            return $division['name'];
        }
    }
    return "Inconnue";
}
?>
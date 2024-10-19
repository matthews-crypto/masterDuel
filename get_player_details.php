<?php
// Fichier: get_player_details.php
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (isset($_GET['player_id'])) {
    $player_id = $_GET['player_id'];
    $player = getPlayer($player_id);
    $divisionRank = getPlayerDivisionRank($player_id);

    if ($player) {
        $response = [
            'pseudo' => $player['pseudo'],
            'main_deck' => $player['main_deck'],
            'secondary_deck' => $player['secondary_deck'],
            'division' => getDivisionName($player['division_id']),
            'division_rank' => $divisionRank
        ];
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Joueur non trouvé']);
    }
} else {
    echo json_encode(['error' => 'ID du joueur non spécifié']);
}

function getPlayerDivisionRank($player_id) {
    $db = getDB();
    $player = getPlayer($player_id);
    
    $stmt = $db->prepare("SELECT COUNT(*) + 1 as rank
                          FROM players p1
                          WHERE p1.division_id = :division_id
                          AND p1.points > (SELECT points FROM players p2 WHERE p2.id = :player_id)");
    $stmt->execute([
        'division_id' => $player['division_id'],
        'player_id' => $player_id
    ]);
    $result = $stmt->fetch();
    return $result['rank'];
}
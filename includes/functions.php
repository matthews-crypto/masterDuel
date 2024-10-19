<?php
// Fichier: includes/functions.php

function createAdmin($username, $password) {
    $db = getDB();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
    return $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword
    ]);
}
?>

<?php
// Fichier: includes/functions.php

require_once 'db_connect.php';

function addPlayer($pseudo, $logo_image, $main_deck, $secondary_deck, $division_id) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO players (pseudo, logo_image, main_deck, secondary_deck, division_id) VALUES (:pseudo, :logo_image, :main_deck, :secondary_deck, :division_id)");
    return $stmt->execute([
        'pseudo' => $pseudo,
        'logo_image' => $logo_image,
        'main_deck' => $main_deck,
        'secondary_deck' => $secondary_deck,
        'division_id' => $division_id
    ]);
}

function updatePlayer($id, $pseudo, $logo_image, $main_deck, $secondary_deck, $division_id) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE players SET pseudo = :pseudo, logo_image = :logo_image, main_deck = :main_deck, secondary_deck = :secondary_deck, division_id = :division_id WHERE id = :id");
    return $stmt->execute([
        'id' => $id,
        'pseudo' => $pseudo,
        'logo_image' => $logo_image,
        'main_deck' => $main_deck,
        'secondary_deck' => $secondary_deck,
        'division_id' => $division_id
    ]);
}

function deletePlayer($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM players WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}

function getPlayer($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM players WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

function getAllPlayers() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM players");
    return $stmt->fetchAll();
}

function getDivisions() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM divisions");
    return $stmt->fetchAll();
}
?>

<?php
// Ajouter ces fonctions à includes/functions.php

function addMatch($player_id, $result) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO matches (player_id, result) VALUES (:player_id, :result)");
    $success = $stmt->execute([
        'player_id' => $player_id,
        'result' => $result
    ]);

    if ($success) {
        updatePlayerPoints($player_id, $result);
    }

    return $success;
}

function updatePlayerPoints($player_id, $result) {
    $points = 0;
    switch ($result) {
        case 'win':
            $points = 3;
            break;
        case 'draw':
            $points = 1;
            break;
        case 'loss':
            $points = 0;
            break;
    }

    $db = getDB();
    $stmt = $db->prepare("UPDATE players SET points = points + :points WHERE id = :player_id");
    return $stmt->execute([
        'points' => $points,
        'player_id' => $player_id
    ]);
}

function getPlayerMatches($player_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM matches WHERE player_id = :player_id ORDER BY match_date DESC");
    $stmt->execute(['player_id' => $player_id]);
    return $stmt->fetchAll();
}

function calculateWinRate($player_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT 
        COUNT(*) as total_matches,
        SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as wins
        FROM matches 
        WHERE player_id = :player_id");
    $stmt->execute(['player_id' => $player_id]);
    $result = $stmt->fetch();

    if ($result['total_matches'] > 0) {
        return ($result['wins'] / $result['total_matches']) * 100;
    }
    return 0;
}
?>
<?php
// Ajouter ces fonctions à includes/functions.php

function getPlayerStats($player_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT 
        COUNT(*) as total_matches,
        SUM(CASE WHEN result = 'win' THEN 1 ELSE 0 END) as wins,
        SUM(CASE WHEN result = 'draw' THEN 1 ELSE 0 END) as draws,
        SUM(CASE WHEN result = 'loss' THEN 1 ELSE 0 END) as losses
        FROM matches 
        WHERE player_id = :player_id");
    $stmt->execute(['player_id' => $player_id]);
    $stats = $stmt->fetch();
    
    $player = getPlayer($player_id);
    $stats['points'] = $player['points'];
    
    if ($stats['total_matches'] > 0) {
        $stats['win_rate'] = ($stats['wins'] / $stats['total_matches']) * 100;
    } else {
        $stats['win_rate'] = 0;
    }
    
    return $stats;
}

// Modifier cette fonction dans includes/functions.php

function getLeaderboard($division_id = null, $global = false) {
    $db = getDB();
    $query = "SELECT p.id, p.pseudo, p.points, p.division_id, d.name as division_name,
              COUNT(m.id) as total_matches,
              SUM(CASE WHEN m.result = 'win' THEN 1 ELSE 0 END) as wins
              FROM players p
              LEFT JOIN matches m ON p.id = m.player_id
              LEFT JOIN divisions d ON p.division_id = d.id";
    
    if (!$global && $division_id === null) {
        // Si aucune division n'est spécifiée et ce n'est pas un classement global,
        // on groupe par division
        $query .= " GROUP BY p.division_id, p.id";
    } elseif ($division_id !== null) {
        $query .= " WHERE p.division_id = :division_id";
        $query .= " GROUP BY p.id";
    } else {
        $query .= " GROUP BY p.id";
    }
    
    $query .= " ORDER BY p.division_id, p.points DESC, wins DESC";
    
    $stmt = $db->prepare($query);
    
    if ($division_id !== null) {
        $stmt->execute(['division_id' => $division_id]);
    } else {
        $stmt->execute();
    }
    
    $leaderboard = $stmt->fetchAll();
    
    foreach ($leaderboard as &$player) {
        $player['win_rate'] = $player['total_matches'] > 0 ? ($player['wins'] / $player['total_matches']) * 100 : 0;
    }
    
    return $leaderboard;
}


function getPlayerRank($player_id) {
    $leaderboard = getLeaderboard();
    foreach ($leaderboard as $index => $player) {
        if ($player['id'] == $player_id) {
            return $index + 1;
        }
    }
    return null;
}
?>
<?php
// Ajouter cette fonction à includes/functions.php

function getTotalMatches() {
    $db = getDB();
    $stmt = $db->query("SELECT COUNT(*) FROM matches");
    return $stmt->fetchColumn();
}
?>
<?php
// Ajouter cette fonction à includes/functions.php

function initializeDivisions() {
    $db = getDB();
    $divisions = ['OBELISK', 'RA', 'SLIFER'];
    $stmt = $db->prepare("INSERT INTO divisions (name) VALUES (:name)");
    
    foreach ($divisions as $division) {
        $stmt->execute(['name' => $division]);
    }
}

// Fonction pour vérifier si les divisions existent déjà
function divisionsExist() {
    $db = getDB();
    $stmt = $db->query("SELECT COUNT(*) FROM divisions");
    return $stmt->fetchColumn() > 0;
}
?>

<?php
// Ajouter cette fonction à includes/functions.php

function getDivisionName($division_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT name FROM divisions WHERE id = :id");
    $stmt->execute(['id' => $division_id]);
    $result = $stmt->fetch();
    return $result ? $result['name'] : 'Inconnue';
}
?>
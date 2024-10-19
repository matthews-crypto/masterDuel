<?php
// Fichier: index.php
require_once 'includes/functions.php';

$divisions = getDivisions();
$leaderboards = [];

foreach ($divisions as $division) {
    $leaderboards[$division['id']] = getLeaderboard($division['id']);
}

$globalLeaderboard = getLeaderboard(null, true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement Master Duel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="header-image"></div>
    
    <div class="container mt-5">
        <h1 class="mb-4">Classement Master Duel</h1>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="global-tab" data-bs-toggle="tab" data-bs-target="#global" type="button" role="tab" aria-controls="global" aria-selected="true">Classement Global</button>
            </li>
            <?php foreach ($divisions as $division): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="division-<?php echo $division['id']; ?>-tab" data-bs-toggle="tab" data-bs-target="#division-<?php echo $division['id']; ?>" type="button" role="tab" aria-controls="division-<?php echo $division['id']; ?>" aria-selected="false"><?php echo htmlspecialchars($division['name']); ?></button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="global" role="tabpanel" aria-labelledby="global-tab">
                <?php echo generateLeaderboardTable($globalLeaderboard); ?>
            </div>
            <?php foreach ($divisions as $division): ?>
                <div class="tab-pane fade" id="division-<?php echo $division['id']; ?>" role="tabpanel" aria-labelledby="division-<?php echo $division['id']; ?>-tab">
                    <?php echo generateLeaderboardTable($leaderboards[$division['id']]); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="download-csv" class="btn btn-primary mt-3">Télécharger le classement (CSV)</button>

        <div id="player-details" style="display: none;" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>document.addEventListener('DOMContentLoaded', function() {
    const divisionFilter = document.getElementById('division-filter');
    const leaderboardTables = document.querySelectorAll('.leaderboard-table');
    const downloadCsvButton = document.getElementById('download-csv');
    const tabButtons = document.querySelectorAll('.nav-link');

    // Gérer les animations des divisions
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            
            // Réinitialiser l'animation
            targetPane.style.animation = 'none';
            targetPane.offsetHeight; // Forcer un reflow
            targetPane.style.animation = null;
        });
    });

    // Fonction pour filtrer les lignes d'un tableau
    function filterTable(table, selectedDivision) {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const divisionId = row.getAttribute('data-division');
            if (selectedDivision === '' || divisionId === selectedDivision) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Événement de changement du filtre de division
    if (divisionFilter) {
        divisionFilter.addEventListener('change', function() {
            const selectedDivision = this.value;
            leaderboardTables.forEach(table => {
                filterTable(table, selectedDivision);
            });
        });
    }

    // Événement de clic sur le bouton de téléchargement CSV
    downloadCsvButton.addEventListener('click', function() {
        const activeTab = document.querySelector('.tab-pane.active');
        const activeTable = activeTab.querySelector('.leaderboard-table');
        let csv = 'Rang,Pseudo,Division,Points,Matchs Joués,Victoires,Taux de Victoire\n';
        
        const rows = activeTable.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                const cells = row.querySelectorAll('td');
                const rowData = Array.from(cells).map(cell => cell.textContent.trim());
                csv += rowData.join(',') + '\n';
            }
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "classement.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });

    // Fonction pour afficher les détails du joueur
    function showPlayerDetails(playerId) {
        fetch(`get_player_details.php?player_id=${playerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    alert(data.error);
                    return;
                }
                const detailsHtml = `
                    <h3>${data.pseudo}</h3>
                    <p><strong>Deck principal:</strong> ${data.main_deck}</p>
                    <p><strong>Deck secondaire:</strong> ${data.secondary_deck}</p>
                    <p><strong>Division:</strong> ${data.division}</p>
                    <p><strong>Rang dans la division:</strong> ${data.division_rank}</p>
                `;
                const detailsContainer = document.getElementById('player-details');
                detailsContainer.innerHTML = detailsHtml;
                detailsContainer.style.display = 'block';
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Une erreur est survenue lors de la récupération des détails du joueur.');
            });
    }

    // Ajouter des écouteurs d'événements pour les lignes du tableau
    leaderboardTables.forEach(table => {
        table.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('click', function() {
                const playerId = this.getAttribute('data-player-id');
                showPlayerDetails(playerId);
            });
        });
    });
});</script>
</body>
</html>

<?php
function generateLeaderboardTable($leaderboard) {
    $html = '<table class="table table-striped leaderboard-table">
        <thead>
            <tr>
                <th>Rang</th>
                <th>Pseudo</th>
                <th>Division</th>
                <th>Points</th>
                <th>Matchs Joués</th>
                <th>Victoires</th>
                <th>Taux de Victoire</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($leaderboard as $index => $player) {
        $html .= '<tr data-player-id="' . $player['id'] . '">
            <td>' . ($index + 1) . '</td>
            <td class="player-name">' . htmlspecialchars($player['pseudo']) . '</td>
            <td>' . htmlspecialchars($player['division_name']) . '</td>
            <td>' . $player['points'] . '</td>
            <td>' . $player['total_matches'] . '</td>
            <td>' . $player['wins'] . '</td>
            <td>' . number_format($player['win_rate'], 2) . '%</td>
        </tr>';
    }
    
    $html .= '</tbody></table>';
    return $html;
}
?>
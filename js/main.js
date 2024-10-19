document.addEventListener('DOMContentLoaded', function() {
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
});
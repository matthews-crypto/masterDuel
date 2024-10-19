<?php
// Fichier: admin/index.php

session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdminLogin();

$totalPlayers = count(getAllPlayers());
$totalMatches = getTotalMatches();
$divisions = getDivisions();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord administratif - Master Duel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_players.php">
                                Gérer les joueurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_matches.php">
                                Gérer les matchs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_divisions.php">
                                Gérer les divisions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                Déconnexion
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tableau de bord</h1>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total des joueurs</h5>
                                <p class="card-text display-4"><?php echo $totalPlayers; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total des matchs</h5>
                                <p class="card-text display-4"><?php echo $totalMatches; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Nombre de divisions</h5>
                                <p class="card-text display-4"><?php echo count($divisions); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h2>Actions rapides</h2>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <a href="manage_players.php" class="btn btn-primary btn-lg btn-block">Ajouter un joueur</a>
                    </div>
                    <div class="col-md-6 mb-4">
                        <a href="manage_matches.php" class="btn btn-success btn-lg btn-block">Enregistrer un match</a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>
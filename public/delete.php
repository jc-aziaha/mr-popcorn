<?php
session_start();

    require_once __DIR__ . "/../functions/helpers.php";
    require_once __DIR__ . "/../functions/db.php";

    // 1. Si les données du formulaire n'ont pas été envoyées via la méthode POST, c'est mort.
    if ( "POST" !== $_SERVER['REQUEST_METHOD'] ) {
        redirectToPage('index');
    }

    // 2. Alors, penser à bien sécuriser l'application contre certains types de failles
    // 2a. Protéger le serveur contre les failles de type csrf
    if ( 
        !isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || 
        empty($_SESSION['csrf_token'])  || empty($_POST['csrf_token'])  ||
        $_SESSION['csrf_token'] !== $_POST['csrf_token']
        ) {
        redirectToPage("index");
    }
    unset($_SESSION['csrf_token']);
    unset($_POST['csrf_token']);


    // 2b. Protéger le serveur contre les robots spameurs
    if ( !isset($_POST['honey_pot']) || ("" !== $_POST['honey_pot']) ) {
        redirectToPage("index");
    }
    unset($_POST['honey_pot']);


    // 3. Récupérer l'identifiant du film
    $filmId = (int) htmlspecialchars($_POST['film_id']);


    // 4. S'assurer que l'identifiant corresponde bien à un film qui existe en base de données, en le récupérant.
    $film = getFilm($filmId);

    // 5. Si jamais le film est introuvable, alors
    if ( false === $film ) {
        // Effectuer une redirection vers la page d'accueil
        // Puis, arrêter l'exaécution du script.
        redirectToPage('index');
    }

    // 6. Dans le contraire, supprimons le film
    deleteFilm($film['id']);

    // 7. Sauvegarder en session le message flash de succès de l'opération
    $_SESSION['success'] = "Le film a bien été supprimé.";

    // 8. Effectuer une redirection vers la page d'accueil,
    // Puis, arrêter l'exécution du script.
    redirectToPage('index');
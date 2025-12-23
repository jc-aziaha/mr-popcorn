<?php

    require_once __DIR__ . "/../functions/helpers.php";
    require_once __DIR__ . "/../functions/db.php";
    require_once __DIR__ . "/../functions/view.php";

    /*
     ******************************************************
     * Affichage des détails de chaque film 
     ****************************************************** 
     */

    // 1. S'assurer que l'identifiant du film dont on souhaite consulter les détails a été envoyé via la méthode GET
    if ( !isset($_GET['film_id']) || empty($_GET['film_id']) ) {
        redirectToPage('index');
    }

    // 2. Récupérer l'identifiant du film, 
    // protéger le serveur contre les failles de type XSS,
    // et le convertir l'identifiant en entier
    $filmId = (int) htmlspecialchars($_GET['film_id']);

    // 3. S'assurer que l'identifiant corresponde bien à un film qui existe en base de données, en le récupérant.
    $film = getFilm($filmId);

    // 4. Si le film est introuvable, alors
    if ( false === $film ) {
        // Effectuer une redirection vers la page d'accueil
        // Puis, arrêter l'exaécution du script.
        redirectToPage('index');
    }

    // 5. Afficher les détails du film, à l'écran de l'utilisateur.
?>
<?php
    $title = "Les détails du film:{$film['title']}";
    $description = "Consulter les détails du film:{$film['title']}";
    $keywords = "détails, film-{$film['id']}, Cinéma";
?>
<?php include_once __DIR__ . "/../partials/head.php"; ?>

    <?php include_once __DIR__ . "/../partials/nav.php"; ?>
        
        <!-- Main: Le contenu spécifique à cette page. -->
        <main class="container">
            <h1 class="text-center display-5 my-3">Les détails du film</h1>
            
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-lg-7 mx-auto">
                        <article class="film-card bg-white shadow rounded p-3 mb-4">
                            <h2>Titre: <?= htmlspecialchars($film['title']); ?></h2>
                            <p><strong>Note</strong>: <?= isset($film['rating']) && $film['rating'] !== "" ? displayStars(htmlspecialchars((float) $film['rating'])) : "Non renseignée"; ?></p>
                            <p><strong>Commentaire</strong>: <?= isset($film['comment']) && "" !== $film['comment'] ? nl2br(htmlspecialchars($film['comment'])) : 'Non renseigné'; ?></p>
                        </article>
                    </div>
                </div>
            </div>

        </main>

    <?php include_once __DIR__ . "/../partials/footer.php"; ?>

<?php include_once __DIR__ . "/../partials/foot.php"; ?>

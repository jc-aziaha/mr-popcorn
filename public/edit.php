<?php
session_start();

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

    // 5. Afficher les détails du film, dans le formulaire de modification.

    /*
     **********************************************************
     * Traitement des données provenant du formulaire 
     ********************************************************** 
     */

    // 6. Si les données arrivent via la méthode POST
    if ( "POST" === $_SERVER['REQUEST_METHOD'] ) {

        // 7. Alors, penser à bien sécuriser l'application contre certains types de failles
        // 7a. Protéger le serveur contre les failles de type csrf
        if ( 
            !isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || 
            empty($_SESSION['csrf_token'])  || empty($_POST['csrf_token'])  ||
            $_SESSION['csrf_token'] !== $_POST['csrf_token']
         ) {
            redirectToPage("edit", $film['id']);
        }
        unset($_SESSION['csrf_token']);
        unset($_POST['csrf_token']);


        // 7b. Protéger le serveur contre les robots spameurs
        if ( !isset($_POST['honey_pot']) || ("" !== $_POST['honey_pot']) ) {
            redirectToPage("edit", $film['id']);
        }
        unset($_POST['honey_pot']);

        // 8. Définir les contraintes de validation des inputs, et préparer les messages d'erreurs correspondant.
        $formErrors = [];

        if ( isset($_POST['title']) ) {
            $title = trim($_POST['title']);

            if ( "" === $title ) {
                $formErrors['title'] = "Le titre est obligatoire.";
            } else if( mb_strlen($title) > 255 ) {
                $formErrors['title'] = "Le titre ne doit pas dépasser 255 caractères.";
            }
        }

        if ( isset($_POST['rating']) && ("" !== $_POST['rating']) ) {
            $rating = trim($_POST['rating']);

            if ( ! is_numeric($rating) ) {
                $formErrors['rating'] = "La note doit être un nombre.";
            } else if( floatval($rating) < 0 || floatval($rating) > 5 ) {
                $formErrors['rating'] = "La note doit être comprise entre 0 et 5.";
            }
        }


        if ( isset($_POST['comment']) && ("" !== $_POST['comment']) ) {
            $comment = trim($_POST['comment']);

            if( mb_strlen($comment) > 1000 ) {
                $formErrors['comment'] = "Le commentaire ne doit pas dépasser 1000 caractères.";
            }
        }

        // 9. Si le système détecte au moins une erreur
        if ( count($formErrors) > 0 ) {

            // 9a. Sauvegarder les messages d'erreurs préparés en session, pour affichage à l'utilisateur
            $_SESSION['form_errors'] = $formErrors;

            // 9b. Sauvegarder les anciennes données provenant du formulaire en session
            $_SESSION['old'] = $_POST;
            
            // 9c. Effectuer une redirection vers la page de laquelle proviennent les informations,
            // puis arrêter l'exécution du script.
            redirectToPage("edit", $film['id']);
        }

        // Dans le cas contraire,
        // 10. Arrondir la note à un chiffre après la virgule
        $ratingRounded = null;

        if ( isset($_POST['rating']) && "" !== $_POST['rating'] ) {
            $ratingRounded = round($_POST['rating'], 1);
        }

        // 11. Etablir une connexion avec la base de données
        // 12. Efectuer la requête d'insertion du nouveau film en base de données
        updateFilm($ratingRounded, (int) $film['id'], $_POST);

        // 13. Sauvegarder en session le message flash de succès de l'opération
        $_SESSION['success'] = "Le film a bien été modifié avec succès.";

        // 14. Effectuer une redirection vers la page d'accueil,
        // Puis, arrêter l'exécution du script.
        redirectToPage('index');
    }

    // Générer une chaine de caractère aléatoire (csrf_token: Jéton de sécurité)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<?php
    $title = "Modification du film:{$film['title']}";
    $description = "Procéder à la modification du film:{$film['title']}";
    $keywords = "modification, film-{$film['id']}, Cinéma";
?>
<?php include_once __DIR__ . "/../partials/head.php"; ?>

    <?php include_once __DIR__ . "/../partials/nav.php"; ?>
        
        <!-- Main: Le contenu spécifique à cette page. -->
        <main class="container">
            <h1 class="text-center display-5 my-3">Modifier ce film</h1>

            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-4 mx-auto bg-white shadow rounded p-4">

                        <?php if( isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors']) ) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul>
                                    <?php foreach($_SESSION['form_errors'] as $error) : ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['form_errors']); ?>
                        <?php endif ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="title">Titre <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" value="<?= isset($_SESSION['old']['title']) && !empty($_SESSION['old']['title']) ? htmlspecialchars($_SESSION['old']['title']) : htmlspecialchars($film['title']); unset($_SESSION['old']['title']); ?>" autofocus required>
                            </div>
                            <div class="mb-3">
                                <label for="rating">Note / 5</label>
                                <input inputmode="decimal" type="number" name="rating" id="rating" class="form-control" value="<?= isset($_SESSION['old']['rating']) && "" !== $_SESSION['old']['rating'] ? htmlspecialchars($_SESSION['old']['rating']) : htmlspecialchars((string) $film['rating']); unset($_SESSION['old']['rating']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="comment">Laissez un commentaire</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4"><?= isset($_SESSION['old']['comment']) && !empty($_SESSION['old']['comment']) ? htmlspecialchars($_SESSION['old']['comment']) : htmlspecialchars($film['comment']); unset($_SESSION['old']['comment']); ?></textarea>
                            </div>
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="honey_pot" value="">
                            <div>
                                <input formnovalidate type="submit" class="btn btn-primary w-100" value="Modifier">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

    <?php include_once __DIR__ . "/../partials/footer.php"; ?>

<?php include_once __DIR__ . "/../partials/foot.php"; ?>
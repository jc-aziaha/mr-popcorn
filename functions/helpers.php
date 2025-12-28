<?php

    /**
     * Cette fonction permet d'afficher une valeur puis d'arrêter l'exécution du script, afin de débogguer.
     *
     * @param mixed $data
     * 
     * @return void
     */
    function dd(mixed $data): void {
        var_dump($data);
        die();
    }

    /**
     * Cette fonction permet d'afficher une valeur sans arrêter l'exécution du script, afin de débogguer.
     *
     * @param mixed $data
     * 
     * @return void
     */
    function dump(mixed $data): void {
        var_dump($data);
    }



    /**
     * Cette fonction effectue une redirection vers la page renseignée, puis arrête l'exécution du script.

     *
     * @param string $pageName
     * @param null|string|integer|null $filmId
     * 
     * @return void
     */
    function redirectToPage(string $pageName, null|string|int $filmId = null): void {

        if ( isset($filmId) && !empty($filmId) ) {
            header("Location: $pageName.php?film_id=$filmId");
        } else {
            header("Location: $pageName.php");
        }

        die();
    }
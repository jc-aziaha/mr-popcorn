<?php

    /**
     * Cette fonction permet de debogger.
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
     * Cette fonction effectue la redirection vers une page.
     *
     * @param string $pageName
     * @param null|integer|string|null $id
     * 
     * @return void
     */
    function redirectToPage(string $pageName, null|int|string $id = null): void {

        if ( isset($id) && !empty($id) ) {
            header("Location: $pageName.php?film_id=$id");
        } else {
            header("Location: $pageName.php");
        }

        die();
    }
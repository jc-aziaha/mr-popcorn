<?php

    /**
     * Cette fonction permet d'établir une connexion avec la base de données.
     *
     * @return PDO
     */
    function connectToDb(): PDO {

        $dsn = 'mysql:dbname=dwwm-mr-popcorn;host=127.0.0.1;port=3306';
        $user = 'root';
        $password = '';

        try {
            $db = new PDO($dsn, $user, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $pdoException) {
            die("Error connection to database: {$pdoException->getMessage()}");
        }

        return $db;
    }


    /**
     * Cette fonction permet d'insérer le nouveau film en base de données.
     *
     * @param null|float $ratingRounded
     * @param array $data
     * 
     * @return void
     */
    function insertFilm(null|float $ratingRounded, array $data = []): void {
        $db = connectToDb();

        try {
            $req = $db->prepare("INSERT INTO film (title, rating, comment, created_at, updated_at) VALUES (:title, :rating, :comment, now(), now() ) ");
    
            $req->bindValue(":title", $data['title']);
            $req->bindValue(":rating", $ratingRounded);
            $req->bindValue(":comment", $data['comment']);
    
            $req->execute();
            $req->closeCursor();
        } catch (\PDOException $pdoException) {
            throw $pdoException;
        }
    }


    /**
     * Cette fonction récupère tous les films de la base de données.
     *
     * @return array
     */
    function getFilms(): array {
        $db = connectToDb();

        try {
            $req = $db->prepare("SELECT * FROM film ORDER BY created_at DESC");
            $req->execute();
            $films = $req->fetchAll();
            $req->closeCursor();
        } catch (\PDOException $pdoException) {
            throw $pdoException;
        }

        return $films;
    }


    /**
     * Cette fonction récupère un film en particulier de la base de données.
     *
     * @param integer $filmId
     * 
     * @return array|bool
     */
    function getFilm(int $filmId): bool|array {
        $db = connectToDb();

        try {
            $req = $db->prepare("SELECT * FROM film WHERE id=:id");
            $req->bindValue(":id", $filmId);
            $req->execute();
            $film = $req->fetch();
            $req->closeCursor();
        } catch (\PDOException $pdoException) {
            throw $pdoException;
        }

        return $film;
    }


    /**
     * Cette fonction permet de modifier un film en base de données.
     *
     * @param float|null $ratingRounded
     * @param array $data
     * 
     * @return void
     */
    function updateFilm(?float $ratingRounded, int $filmId, array $data = []): void {
        $db = connectToDb();

        try {
            $req = $db->prepare("UPDATE film SET title=:title, rating=:rating, comment=:comment, updated_at=now() WHERE id=:id");
    
            $req->bindValue(":title", $data['title']);
            $req->bindValue(":rating", $ratingRounded);
            $req->bindValue(":comment", $data['comment']);
            $req->bindValue(":id", $filmId);
    
            $req->execute();
            $req->closeCursor();
        } catch (\PDOException $pdoException) {
            throw $pdoException;
        }
    }
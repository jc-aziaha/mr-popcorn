<?php include_once __DIR__ . "/../partials/head.php"; ?>

    <?php include_once __DIR__ . "/../partials/nav.php"; ?>
        
        <!-- Main: Le contenu spécifique à cette page. -->
        <main class="container">
            <h1 class="text-center display-5 my-3">Nouveau film</h1>

            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-4 mx-auto bg-white shadow rounded p-4">

                        <form method="post">
                            <div class="mb-3">
                                <label for="title">Titre <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" autofocus required>
                            </div>
                            <div class="mb-3">
                                <label for="rating">Note / 5</label>
                                <input inputmode="decimal" type="number" name="rating" id="rating" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="comment">Laissez un commentaire</label>
                                <textarea name="comment" id="comment" class="form-control" rows="4"></textarea>
                            </div>
                            <div>
                                <input type="submit" class="btn btn-primary w-100" value="Ajouter">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

    <?php include_once __DIR__ . "/../partials/footer.php"; ?>

<?php include_once __DIR__ . "/../partials/foot.php"; ?>
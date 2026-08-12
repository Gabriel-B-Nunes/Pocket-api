<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pocket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <div class="d-flex container-fluid vh-100 align-items-center justify-content-center">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="row align-items-center justify-content-center">
                <img src="pocket_logo.png" class="img-fluid w-50" alt="">
            </div>
            <div class="row h-75">
                <form action="">
                    <div class="row p-2">
                        <label for=""><?= _("Email") ?></label>
                        <input type="email" class="form-control" id="inputEmail" aria-describedby="emailHelp">
                        <div id="emailHelp" class="form-text"><?= _("Example: example@domain.com") ?></div>
                    </div>

                    <div class="row p-2">
                        <label for=""><?= _("Password") ?></label>
                        <input type="password" class="form-control" id="inputPassword">
                        <p><a href="#" class="link-secondary"><?= _("Forgot your password?") ?></a></p>
                    </div>

                    <div class="row p-2">
                        <button type="button" class="btn btn-primary"><?= _("Login") ?></button>
                    </div>
                </form>
            </div>
            <div class="row">
                <p><a href="userView.php" class="link-secondary"><?= _("Don't have an account? Create one!") ?></a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>
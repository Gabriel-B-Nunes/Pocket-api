<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\SuperGroup;
use App\service\ListingManager;

$listingManager = ListingManager::postConstructor(SuperGroup::getConstructor([]));
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pocket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <div class="container-fluid">
        <? include $_SERVER["DOCUMENT_ROOT"] . "/template/navbar.php" ?>
    </div>

    <div class="container-fluid d-flex justify-content-center">
        <form action="<?= $listingManager->getClassName() ?>" method="POST" class="row row-cols-lg-auto align-items-center g-2 p-2">
            <input type="hidden" name="superGroupId" value="<?= $listingManager->getRegistrations()[0]["superGroupId"] ?? null ?>">

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupId"><?= _("Id") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Id") ?></div>
                    <input type="text" class="form-control" name="superGroupId" id="inlineFormInputGroupId" disabled readonly value="<?= $listingManager->getRegistrations()[0]["superGroupId"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupName"><?= _("Name") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Name") ?></div>
                    <input type="text" class="form-control" name="superGroupName" id="inlineFormInputGroupName" value="<?= $listingManager->getRegistrations()[0]["superGroupName"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupStatus"><?= _("Status") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Status") ?></div>
                    <select class="form-select" name="superGroupStatus" id="inlineFormInputGroupStatus">
                        <option value="0" <?= isset($listingManager->getRegistrations()[0]["superGroupStatus"]) && $listingManager->getRegistrations()[0]["superGroupStatus"] == 0 ? "selected" : "" ?>><?= _("Active") ?></option>
                        <option value="1" <?= isset($listingManager->getRegistrations()[0]["superGroupStatus"]) && $listingManager->getRegistrations()[0]["superGroupStatus"] == 1 ? "selected" : "" ?>><?= _("Blocked") ?></option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="action" value="create"><?= _("Save") ?></button>
                <button type="submit" class="btn btn-primary" name="action" value="delete"><?= _("Delete") ?></button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>
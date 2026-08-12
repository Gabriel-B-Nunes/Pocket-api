<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Responsible;
use App\service\ListingManager;

$listingManager = ListingManager::postConstructor(Responsible::getConstructor([]));
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
            <input type="hidden" name="responsibleId" value="<?= $listingManager->getRegistrations()[0]["responsibleId"] ?? null ?>">

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupId"><?= _("Id") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Id") ?></div>
                    <input type="text" class="form-control" name="responsibleId" id="inlineFormInputGroupId" disabled readonly value="<?= $listingManager->getRegistrations()[0]["responsibleId"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupName"><?= _("Name") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Name") ?></div>
                    <input type="text" class="form-control" name="responsibleName" id="inlineFormInputGroupName" value="<?= $listingManager->getRegistrations()[0]["responsibleName"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupCellphoneNumber"><?= _("Cellphone Number") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Cellphone Number") ?></div>
                    <input type="text" class="form-control" name="responsibleCellphoneNumber" id="inlineFormInputGroupCellphoneNumber" value="<?= $listingManager->getRegistrations()[0]["responsibleCellphoneNumber"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupEmailAddress"><?= _("Email Address") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Email Address") ?></div>
                    <input type="text" class="form-control" name="responsibleEmailAddress" id="inlineFormInputGroupEmailAddress" value="<?= $listingManager->getRegistrations()[0]["responsibleEmailAddress"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupStatus"><?= _("Status") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Status") ?></div>
                    <select class="form-select" name="responsibleStatus" id="inlineFormInputGroupStatus">
                        <option value="0" <?= isset($listingManager->getRegistrations()[0]["responsibleStatus"]) && $listingManager->getRegistrations()[0]["responsibleStatus"] == 0 ? "selected" : "" ?>><?= _("Active") ?></option>
                        <option value="1" <?= isset($listingManager->getRegistrations()[0]["responsibleStatus"]) && $listingManager->getRegistrations()[0]["responsibleStatus"] == 1 ? "selected" : "" ?>><?= _("Blocked") ?></option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="action" value="create"><?= _("Save") ?></button>
                <button type="submit" class="btn btn-primary" name="action" value="delete"><?= _("Delete") ?></button>
                <a class="btn btn-primary" href="<?= $listingManager->getClassViewName() ?>"><?= _("New") ?></a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Receivable;
use App\model\RecurringEnum;
use App\service\ListingManager;

$listingManager = ListingManager::postConstructor(Receivable::getConstructor([]));
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
            <input type="hidden" name="movementId" value="<?= $listingManager->getRegistrations()[0]["movementId"] ?? null ?>">

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupMovementId"><?= _("Id") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Id") ?></div>
                    <input type="text" class="form-control" name="movementId" id="inlineFormInputGroupMovementId" disabled readonly value="<?= $listingManager->getRegistrations()[0]["movementId"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupMovementDescription"><?= _("Description") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Description") ?></div>
                    <input type="text" class="form-control" name="movementDescription" id="inlineFormInputGroupMovementDescription" value="<?= $listingManager->getRegistrations()[0]["movementDescription"] ?? null ?>">
                </div>
            </div>

            <input type="hidden" name="movementFinancialType" value="0">

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupMovementRecurring"><?= _("Recurring?") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Recurring?") ?></div>
                    <select class="form-select" name="movementRecurring" id="inlineFormInputGroupMovementRecurring">
                        <?php foreach (RecurringEnum::casesView() as $case): ?>
                            <option value="<?= $case->value ?>" <?= (isset($listingManager->getRegistrations()[0]["movementRecurring"]) && $listingManager->getRegistrations()[0]["movementRecurring"] == $case->value) ? "selected" : "" ?>><?= $case->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupMovementIssueDate"><?= _("Issue Date") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Issue Date") ?></div>
                    <input type="date" class="form-control" name="movementIssueDate" id="inlineFormInputGroupMovementIssueDate" value="<?= $listingManager->getRegistrations()[0]["movementIssueDate"] ?? null ?>">
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
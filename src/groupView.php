<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\SuperGroup;
use App\model\StatusEnum;
use App\model\Group;
use App\service\ListingManager;

$listingManager = ListingManager::postConstructor(Group::getConstructor([]));
$superGroups = $listingManager->getDao()->readAllStatusActive(SuperGroup::getConstructor([null]));
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
        <form action="/<?= $listingManager->getClassName() ?>" method="POST" class="row row-cols-lg-auto align-items-center g-2 p-2">
            <input type="hidden" name="groupId" value="<?= $listingManager->getRegistrations()[0]["groupId"] ?? null ?>">

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupId"><?= _("Id") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Id") ?></div>
                    <input type="text" class="form-control" name="groupId" id="inlineFormInputGroupId" disabled readonly value="<?= $listingManager->getRegistrations()[0]["groupId"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupName"><?= _("Name") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Name") ?></div>
                    <input type="text" class="form-control" name="groupName" id="inlineFormInputGroupName" value="<?= $listingManager->getRegistrations()[0]["groupName"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupGroupId"><?= _("Super Group") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Super Group") ?></div>
                    <select class="form-select" name="superGroupId" id="inlineFormInputGroupGroupId">
                        <?php foreach ($superGroups as $superGroup): ?>
                            <?php
                            $superGroupObject = SuperGroup::postConstructor($superGroup);
                            ?>
                            <?php if (isset($listingManager->getRegistrations()[0]["superGroupId"]) && $listingManager->getRegistrations()[0]["superGroupId"] == $superGroupObject->getId()): ?>
                                <option value="<?= $superGroupObject->getId() ?>" selected><?= $superGroupObject->getName() ?></option>
                            <?php else: ?>
                                <option value="<?= $superGroupObject->getId() ?>"><?= $superGroupObject->getName() ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupStatus"><?= _("Status") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Status") ?></div>
                    <select class="form-select" name="groupStatus" id="inlineFormInputGroupStatus">
                        <?php foreach (StatusEnum::casesView() as $case): ?>
                            <option value="<?= $case->value ?>" <?= (isset($listingManager->getRegistrations()[0]["groupStatus"]) && $listingManager->getRegistrations()[0]["groupStatus"] == $case->value) ? "selected" : "" ?>><?= $case->name ?></option>
                        <?php endforeach; ?>
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
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\Item;
use App\model\Group;
use App\model\MeasurementUnitEnum;
use App\model\FinancialTypeEnum;
use App\model\StatusEnum;
use App\service\ListingManager;

$listingManager = ListingManager::postConstructor(Item::getConstructor([]));
$groups = $listingManager->getDao()->readAllStatusActive(Group::getConstructor([null]));
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
            <input type="hidden" name="itemId" value="<?= $listingManager->getRegistrations()[0]["itemId"] ?? null ?>">

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupId"><?= _("Id") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Id") ?></div>
                    <input type="text" class="form-control" name="itemId" id="inlineFormInputGroupId" disabled readonly value="<?= $listingManager->getRegistrations()[0]["itemId"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupName"><?= _("Name") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Name") ?></div>
                    <input type="text" class="form-control" name="itemName" id="inlineFormInputGroupName" value="<?= $listingManager->getRegistrations()[0]["itemName"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupMeasurementUnit"><?= _("Measurement Unit") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Measurement Unit") ?></div>
                    <select class="form-select" name="itemMeasurementUnit" id="inlineFormInputGroupMeasurementUnit">
                        <?php foreach (MeasurementUnitEnum::casesView() as $case): ?>
                            <?php if (isset($listingManager->getRegistrations()[0]["itemMeasurementUnit"]) && $listingManager->getRegistrations()[0]["itemMeasurementUnit"] == $case->value): ?>
                                <option value="<?= $case->value ?>" selected><?= $case->name ?></option>
                            <?php else: ?>
                                <option value="<?= $case->value ?>"><?= $case->name ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupGroupId"><?= _("Group") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Group") ?></div>
                    <select class="form-select" name="groupId" id="inlineFormInputGroupGroupId">
                        <?php foreach ($groups as $group): ?>
                            <?php
                            $groupObject = Group::postConstructor($group);
                            ?>
                            <?php if (isset($listingManager->getRegistrations()[0]["groupId"]) && $listingManager->getRegistrations()[0]["groupId"] == $groupObject->getId()): ?>
                                <option value="<?= $groupObject->getId() ?>" selected><?= $groupObject->getName() ?></option>
                            <?php else: ?>
                                <option value="<?= $groupObject->getId() ?>"><?= $groupObject->getName() ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupFinancialType"><?= _("Financial Type") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Financial Type") ?></div>
                    <select class="form-select" name="itemFinancialType" id="inlineFormInputGroupFinancialType">
                        <?php foreach (FinancialTypeEnum::casesView() as $case): ?>
                            <?php if (isset($listingManager->getRegistrations()[0]["itemFinancialType"]) && $listingManager->getRegistrations()[0]["itemFinancialType"] == $case->value): ?>
                                <option value="<?= $case->value ?>" selected><?= $case->name ?></option>
                            <?php else: ?>
                                <option value="<?= $case->value ?>"><?= $case->name ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupStatus"><?= _("Status") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Status") ?></div>
                    <select class="form-select" name="itemStatus" id="inlineFormInputGroupStatus">
                        <?php foreach (StatusEnum::casesView() as $case): ?>
                            <?php if (isset($listingManager->getRegistrations()[0]["itemStatus"]) && $listingManager->getRegistrations()[0]["itemStatus"] == $case->value): ?>
                                <option value="<?= $case->value ?>" selected><?= $case->name ?></option>
                            <?php else: ?>
                                <option value="<?= $case->value ?>"><?= $case->name ?></option>
                            <?php endif; ?>
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
    <script src="https://jsdelivr.net"></script>
    <script>
        new TomSelect("#inlineFormGroupId", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>
</body>

</html>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\StatusEnum;
use App\model\Group;
use App\model\Item;
use App\model\MeasurementUnitEnum;
use App\model\FinancialTypeEnum;
use App\service\ListingManager;

$listingManager = ListingManager::getConstructor(Item::getConstructor([]));
$groups = $listingManager->getDao()->readAllStatusActive(Group::getConstructor([null]));
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pocket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.6.2/dist/css/tom-select.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <? include $_SERVER["DOCUMENT_ROOT"] . "/template/navbar.php" ?>
    </div>

    <div class="container-fluid d-flex justify-content-center">
        <form action="/<?= $listingManager->getClassName() ?>" method="GET" class="row row-cols-lg-auto align-items-center g-2 p-2">
            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupItemId"><?= _("Id") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Id") ?></div>
                    <input type="text" class="form-control" name="itemId" id="inlineFormInputGroupItemId" value="<?= $_GET["itemId"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupItemName"><?= _("Name") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Name") ?></div>
                    <input type="text" class="form-control" name="itemName" id="inlineFormInputGroupItemName" value="<?= $_GET["itemName"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupItemMeasurementUnit"><?= _("Measurement Unit") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Measurement Unit") ?></div>
                    <select class="form-select" name="itemMeasurementUnit" id="inlineFormInputGroupItemMeasurementUnit">
                        <?php foreach (MeasurementUnitEnum::cases() as $case): ?>
                            <?php if (isset($_GET["itemMeasurementUnit"]) && $_GET["itemMeasurementUnit"] == $case->value): ?>
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
                    <select class="form-select" name="groupId" id="inlineFormInputGroupGroupId" placeholder="<?= _("Select an group...") ?>" autocomplete="off">
                        <option value=""><?= _("Select an group...") ?></option>
                        <option value="-1"><?= _("ALL") ?></option>
                        <?php foreach ($groups as $group): ?>
                            <?php
                            $groupObject = Group::postConstructor($group);
                            ?>
                            <?php if (isset($_GET["groupId"]) && $_GET["groupId"] == $groupObject->getId()): ?>
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
                        <?php foreach (FinancialTypeEnum::cases() as $case): ?>
                            <?php if (isset($_GET["itemFinancialType"]) && $_GET["itemFinancialType"] == $case->value): ?>
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
                        <?php foreach (StatusEnum::cases() as $case): ?>
                            <?php if (isset($_GET["itemStatus"]) && $_GET["itemStatus"] == $case->value): ?>
                                <option value="<?= $case->value ?>" selected><?= $case->name ?></option>
                            <?php else: ?>
                                <option value="<?= $case->value ?>"><?= $case->name ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupitemCreatedAt"><?= _("Created At") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Created At") ?></div>
                    <input type="datetime-local" class="form-control" name="itemCreatedAt" id="inlineFormInputGroupMovementIssueDate" value="<?= $_GET["itemCreatedAt"] ?? null ?>">
                    <div class="input-group-text"><?= _("To") ?></div>
                    <input type="datetime-local" class="form-control" name="itemCreatedAtEnd" id="inlineFormInputGroupMovementIssueDate" value="<?= $_GET["itemCreatedAtEnd"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="action" value="search"><?= _("Search") ?></button>
                <a class="btn btn-primary" href="<?= $listingManager->getClassName() ?>"><?= _("Clear") ?></a>
                <a class="btn btn-secondary" href="<?= $listingManager->getClassViewName() ?>"><?= _("Create") ?></a>
            </div>
        </form>
    </div>

    <div class="container-fluid">
        <div class="table-responsible">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?= _("id") ?></th>
                        <th scope="col"><?= _("Name") ?></th>
                        <th scope="col"><?= _("Measurement Unit") ?></th>
                        <th scope="col"><?= _("Group") ?></th>
                        <th scope="col"><?= _("Status") ?></th>
                        <th scope="col"><?= _("Created At") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listingManager->getRegistrations() as $item): ?>
                        <?php
                        $itemObject = Item::postConstructor($item);
                        ?>
                        <tr>
                            <th scope="row"><a class="link-primary" href="<?= $listingManager->getClassViewName() ?>?itemId=<?= $itemObject->getId() ?>"><?= $itemObject->getId() ?></a></th>
                            <td><?= $itemObject->getName() ?></td>
                            <td><?= $itemObject->getMeasurementUnit() ?></td>
                            <td><?= htmlspecialchars($item["groupName"]) ?></td>
                            <td><?= $itemObject->getStatus() ?></td>
                            <td><?= $itemObject->getCreatedAt() ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($listingManager->getPages() > 1): ?>
        <div class="container-fluid d-flex justify-content-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item <?= ($listingManager->getCurrentPage() <= 1) ? "disabled" : "" ?>">
                        <?php
                        $listingManager->updateQueryParams(["page" => $listingManager->getCurrentPage() - 1]);
                        $previousUrl = "?" . http_build_query($listingManager->getQueryParams());
                        ?>
                        <a class="page-link" href="<?= $previousUrl ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $listingManager->getPages(); $i++): ?>
                        <?php
                        $listingManager->updateQueryParams(["page" => $i]);
                        $pageUrl = "?" . http_build_query($listingManager->getQueryParams());
                        ?>
                        <li class="page-item <?= ($listingManager->getCurrentPage() == $i) ? "active" : "" ?>"><a class="page-link" href="<?= $pageUrl ?>"><?= $i ?></a></li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($listingManager->getCurrentPage() >= $listingManager->getPages()) ? "disabled" : "" ?>">
                        <?php
                        $listingManager->updateQueryParams(["page" => $listingManager->getCurrentPage() + 1]);
                        $nextUrl = "?" . http_build_query($listingManager->getQueryParams());
                        ?>
                        <a class="page-link" href="<?= $nextUrl ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.6.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect("#inlineFormInputGroupGroupId", {
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>
</body>

</html>
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/autoload.php';

use App\model\SuperGroup;
use App\service\ListingManager;

$listingManager = ListingManager::getConstructor(SuperGroup::getConstructor([]));
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
        <form action="<?= $listingManager->getClassName() ?>" method="GET" class="row row-cols-lg-auto align-items-center g-2 p-2">
            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupSuperGroupId"><?= _("Id") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Id") ?></div>
                    <input type="text" class="form-control" name="superGroupId" id="inlineFormInputGroupSuperGroupId" value="<?= $_GET["superGroupId"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupSuperGroupName"><?= _("Name") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Name") ?></div>
                    <input type="text" class="form-control" name="superGroupName" id="inlineFormInputGroupSuperGroupName" value="<?= $_GET["superGroupName"] ?? null ?>">
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupSuperGroupStatus"><?= _("Status") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Status") ?></div>
                    <select class="form-select" name="superGroupStatus" id="inlineFormInputGroupSuperGroupStatus">
                        <option value="-1" <?= isset($_GET["superGroupStatus"]) && $_GET["superGroupStatus"] == -1 ? "selected" : "" ?>><?= _("ALL") ?></option>
                        <option value="0" <?= isset($_GET["superGroupStatus"]) && $_GET["superGroupStatus"] == 0 ? "selected" : "" ?>><?= _("Active") ?></option>
                        <option value="1" <?= isset($_GET["superGroupStatus"]) && $_GET["superGroupStatus"] == 1 ? "selected" : "" ?>><?= _("Blocked") ?></option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="visually-hidden" for="inlineFormInputGroupSuperGroupCreateAt"><?= _("Created At") ?></label>
                <div class="input-group">
                    <div class="input-group-text"><?= _("Created At") ?></div>
                    <input type="datetime-local" class="form-control" name="superGroupCreatedAt" id="inlineFormInputGroupSuperGroupCreateAt" value="<?= $_GET["superGroupCreatedAt"] ?? null ?>">
                    <div class="input-group-text"><?= _("To") ?></div>
                    <input type="datetime-local" class="form-control" name="superGroupCreatedAtEnd" id="inlineFormInputGroupSuperGroupCreateAtEnd" value="<?= $_GET["superGroupCreatedAtEnd"] ?? null ?>">
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
                        <th scope="col"><?= _("Status") ?></th>
                        <th scope="col"><?= _("Created At") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listingManager->getRegistrations() as $superGroup): ?>
                        <?php
                        $superGroupObject = SuperGroup::postConstructor($superGroup);
                        ?>
                        <tr>
                            <th scope="row"><a class="link-primary" href="<?= $listingManager->getClassViewName() ?>?superGroupId=<?= $superGroupObject->getId() ?>"><?= $superGroupObject->getId() ?></a></th>
                            <td><?= $superGroupObject->getName() ?></td>
                            <td><?= $superGroupObject->getStatus() ?></td>
                            <td><?= $superGroupObject->getCreatedAt() ?></td>
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
</body>

</html>
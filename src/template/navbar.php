<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="home.php">
            <img src="pocket_logo.png" alt="Pocket" class="img-fluid" width="50" height="50">
        </a>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= _("Registrations") ?></a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/responsible.php"><?= _("Responsible") ?></a></li>
                        <li><a class="dropdown-item" href="/superGroup.php"><?= _("Super Group") ?></a></li>
                        <li><a class="dropdown-item" href="/group.php"><?= _("Group") ?></a></li>
                        <li><a class="dropdown-item" href="/item.php"><?= _("Item") ?></a></li>
                        <li><a class="dropdown-item" href="/receivable.php"><?= _("Receivable") ?></a></li>
                        <li><a class="dropdown-item" href="/payable.php"><?= _("Payable") ?></a></li>
                        <li><a class="dropdown-item" href="#"><?= _("Installment") ?></a></li>
                        <li><a class="dropdown-item" href="#"><?= _("Transaction") ?></a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= _("Control Panels") ?></a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><?= _("Pay") ?></a></li>
                        <li><a class="dropdown-item" href="#"><?= _("Receive") ?></a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= _("Reports") ?></a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item"><?= _("Coming soon...") ?></span></li>
                    </ul>
                </li>
        </div>
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <svg id="i-settings" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="M13 2 L13 6 11 7 8 4 4 8 7 11 6 13 2 13 2 19 6 19 7 21 4 24 8 28 11 25 13 26 13 30 19 30 19 26 21 25 24 28 28 24 25 21 26 19 30 19 30 13 26 13 25 11 28 8 24 4 21 7 19 6 19 2 Z" />
                        <circle cx="16" cy="16" r="4" />
                    </svg>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg-end">
                    <li class="dropstart">
                        <a class="nav-link dropdown-toggle dropdown-item" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= _("Language") ?></a>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item btn"><?= _("Portuguese") ?></button></li>
                            <li><button class="dropdown-item btn"><?= _("English") ?></button></li>
                        </ul>
                    </li>
                    <li><a class="dropdown-item" href="parameters.php"><?= _("Parameters") ?></a></li>
                </ul>
            </li>
        </ul>
    </div>
    <button class="navbar-toggler bg-transparent border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>
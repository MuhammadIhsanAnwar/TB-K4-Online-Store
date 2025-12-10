<?php
$menu_active = $_GET['menu'] ?? 'profil';
?>

<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 230px; height:100vh; position:fixed;">
    <a href="settings.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4">Settings</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="settings.php?menu=profil" class="nav-link text-white <?= $menu_active == 'profil' ? 'active bg-secondary' : '' ?>">
                🧑 Edit Profil
            </a>
        </li>
        <li>
            <a href="settings.php?menu=payment" class="nav-link text-white <?= $menu_active == 'payment' ? 'active bg-secondary' : '' ?>">
                💳 Metode Pembayaran
            </a>
        </li>
        <li>
            <a href="settings.php?menu=lain" class="nav-link text-white <?= $menu_active == 'lain' ? 'active bg-secondary' : '' ?>">
                ⚙ Pengaturan Lainnya
            </a>
        </li>
    </ul>
</div>

<style>
    .nav-link:hover {
        background-color: #495057 !important;
    }
</style>

<?php 
$logged_in = $logged_in ?? false;
?>
<nav class="w-full bg-linear-to-r from-black/75 to-stone-500/75 flex justify-between gap-10 px-25">
    <a href="/home">
        <img src="/assets/images/logo.jpg" alt="logo" class="h-full w-32 object-cover">
    </a>
    <div class="flex justify-between gap-5 items-center">
        <?php require_once __DIR__ . '/profile.view.php'; ?>
        <img src="/assets/icons/cart.svg" alt="Cart icon" class="w-5">
    </div>
</nav>
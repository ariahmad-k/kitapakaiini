<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading"></div>
            <a class="nav-link" href="../menu/data_menu.php">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Data Menu
            </a>
            <a class="nav-link" href="../stok/data_stock.php">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Stok Harian
            </a>
           
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Admin:</div>
           <?php 
            echo isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Unknown';
        ?>
    </div>
</nav>
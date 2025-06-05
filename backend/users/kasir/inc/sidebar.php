<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading"></div>
            <a class="nav-link" href="#">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Input Pesanan
            </a>
           <a class="nav-link" href="#">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Pesanan Masuk
            </a>
            <a class="nav-link" href="#">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Riyawat Pesanan
            </a> 
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Kasir :</div>
        <?php
        echo isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Unknown';
        ?>
    </div>
</nav>
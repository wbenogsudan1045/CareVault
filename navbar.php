<head>
    <link rel="stylesheet" href="css/navbar.css">
</head>

<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$homeLink = (isset($_SESSION['role']) && $_SESSION['role'] === 'Doctor') ? 'dr-tab-home.php' : 'index.php';
?>
<nav id="desktop-nav">
    <div class="logo-container">
        <a href="index.php"><img src="images/logo.png" alt="" class="logo-pic"></a>
    </div>
    <div class="nav-shortcuts">
        <ul class="nav-links">
            <li><a href="medical_record.php">Medical Records</a></li>
            <li><a href="medication.php">Medications</a></li>
            <li><a href="vaccination.php">Vaccination</a></li>
            <li><a href="archive.php">Archive</a></li>
        </ul>
    </div>
    <div class="nav-links-side">
        <a href="profile.php" class="text-danger">
            <?= $_SESSION['username'] ?> <?= $_SESSION['id'] ?>
        </a>
        <a href="logout.php">Log out</a>

    </div>
</nav>
<nav id="humburger-nav">
    <div class="logo-container">
        <a href="index.php"><img src="images/logo.png" alt="" class="logo-pic"></a>
        <!--<img src="images/logo.png" alt="" class="logo-pic"> -->
    </div>
    <div class="humburger-menu">
        <div class="humburger-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="menu-links">
            <li><a href="medical_record.php" onclick="toggleMenu()">Medical Records</a></li>
            <li><a href="medication.php" onclick="toggleMenu()">Medications</a></li>
            <li><a href="vaccination.php" onclick="toggleMenu()">Vaccination</a></li>
            <li><a href="archive.php" onclick="toggleMenu()">Archive</a></li>
            <li>
                <a href="profile.php" class="text-danger" onclick="toggleMenu()">
                    <?= $_SESSION['username'] ?> <?= $_SESSION['id'] ?>
                </a>
            </li>
            <li><a href="logout.php" onclick="toggleMenu()">Log out</a></li>
        </div>
    </div>
</nav>
<script src="script.js"></script>
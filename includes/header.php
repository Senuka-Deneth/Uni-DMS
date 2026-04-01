<?php // header.php - Reusable site header ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Degree Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js" defer></script>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Uni-DMS</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="universities.php">Universities</a></li>
                <li><a href="finder.php">Z-Score Finder</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="about.php">About</a></li>
                <li><button id="theme-toggle" class="btn-theme" aria-label="Toggle Dark Mode">🌙</button></li>
            </ul>
        </div>
    </nav>
</header>
<main>

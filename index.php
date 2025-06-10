<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="css/nav_bar.css">
    <title>épi doré | Accueil</title>
</head>
<body>
    <nav class="navbar">
    <a href="index.php" class="navbar__logo">
        <img src="images/logo.png" alt="épi doré logo">
    </a>
    <ul class="navbar__links">
        <li class="active"><a href="index.php">Accueil</a></li>
        <li><a href="#">Panier</a></li>
        <?php if (isset($_SESSION['userID'])): ?>
        <li><a href="php/profile.php">Mon Profil</a></li>
        <li><a href="php/logout.php">Déconnexion</a></li>
        <?php else: ?>
        <li><a href="php/connection.php">Connexion</a></li>
        <li><a href="php/inscription.php">Inscription</a></li>
        <?php endif; ?>
    </ul>
    </nav>
</body>
</html>
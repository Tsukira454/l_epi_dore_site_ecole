<?php
session_start();
if (isset($_SESSION['userID'])) {
    //récup le nbr d'article dans le panier
    $_SESSION["panier_nbr_article"] = 0; // Initialiser le compteur du panier
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="css/nav_bar.css">
    <link rel="stylesheet" href="css/index.css">
    <title>épi doré | Accueil</title>
</head>
<body>
    <nav class="navbar">
    <a href="index.php" class="navbar__logo">
        <img src="images/logo.png" alt="épi doré logo">
    </a>
    <ul class="navbar__links">
        <li class="active"><a href="index.php">Accueil</a></li>
        <li><a href="php/note.php">Note au prof</a></li>
        <?php if (isset($_SESSION['userID'])): ?>
        <li><a href="#">Panier <?php if($_SESSION["panier_nbr_article"] > 0) { echo "($_SESSION['panier_nbr_article'])" } ?></a></li>
        <li><a href="php/profile.php">Mon Profil</a></li>
        <li><a href="php/logout.php">Déconnexion</a></li>
        <?php else: ?>
        <li><a href="php/connection.php">Connexion</a></li>
        <li><a href="php/inscription.php">Inscription</a></li>
        <?php endif; ?>
    </ul>
    </nav>
    <div class="content">
        <h1>Bienvenue sur le site de l'épi doré</h1>
        <p>Découvrez nos produits frais et locaux.</p>

    </div>
    <footer class="footer">
        <p>2025 épi doré. Réalisé par Elvin Mouyart</p>
        <p>Contact: <a href="mailto:mouyelv@sjb-liege.org">ici par mail !</a></p>
    </footer>
</body>
</html>
<?php
session_start();
require_once(__DIR__ . "/php/dbConnect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['userID'])) {
        try {
            if (isset($_POST['sandwich_id']) && isset($_SESSION['userID'])) {
                $sandwichId = (int)$_POST['sandwich_id'];
                $sandwichStock = (int)$_POST['sandwich_stock'];
                if ($sandwichStock > 0) {
                    // Vérifie si le sandwich est déjà dans le panier
                    $request = $dbEpidore->prepare("SELECT * FROM ligne_commande WHERE fk_client = :id_clients AND fk_produit = :sandwich_id");
                    $request->execute([
                        ":id_clients" => $_SESSION['userID'],
                        ":sandwich_id" => $sandwichId
                    ]);
                    $results = $request->fetchAll();

                    if (count($results) > 0) {
                        $request_new_ligne = $dbEpidore->prepare("UPDATE ligne_commande SET quantite = quantite + 1 WHERE fk_client = :id_clients AND fk_produit = :sandwich_id");
                    } else {
                        $request_new_ligne = $dbEpidore->prepare("INSERT INTO ligne_commande (fk_client, fk_produit, quantite) VALUES (:id_clients, :sandwich_id, 1)");
                    }

                    $request_new_ligne->execute([
                        ":id_clients" => $_SESSION['userID'],
                        ":sandwich_id" => $sandwichId
                    ]);

                    // Met à jour le stock du sandwich
                    $request_stock = $dbEpidore->prepare("UPDATE sandwichs SET stock = stock - 1 WHERE id_sandwichs = :sandwich_id");
                    $request_stock->execute([":sandwich_id" => $sandwichId]);

                    $request = $dbEpidore->prepare("SELECT * FROM ligne_commande WHERE fk_client = :id_clients");
                    $request->execute([":id_clients" => $_SESSION['userID']]);
                    $_SESSION["panier_nbr_article"] = count($request->fetchAll());

                    header('Location: ./index.php');
                    exit();
                } else {
                    $errorMessage = "Le sandwich est en rupture de stock.";
                }
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    } else {
        $errorMessage = "Vous devez être connecté pour ajouter des articles au panier.";
    }
}

if (isset($_SESSION['userID'])) {
    $nbr = 0;
    $request = $dbEpidore->prepare("SELECT * FROM ligne_commande WHERE fk_client = :id_clients");
    $request->execute([":id_clients" => $_SESSION['userID']]);
    $results = $request->fetchAll();
    foreach ($results as $result) {
        $nbr += $result['quantite'];
    }
    $_SESSION["panier_nbr_article"] = $nbr;
}
// Récupération des sandwichs
$request_sandwichs = $dbEpidore->prepare("SELECT * FROM sandwichs");
$request_sandwichs->execute();
$results = $request_sandwichs->fetchAll();
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
            <?php if (isset($_SESSION['userID'])): ?>
            <li><a href="php/panier.php">Panier <?php if($_SESSION["panier_nbr_article"] > 0) { echo ($_SESSION['panier_nbr_article']); } ?></a></li>
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
        <?php if (isset($errorMessage)) echo "<p style='color: red;'>$errorMessage</p>"; ?>
        <div class="sandwich-list">
            <?php foreach ($results as $sandwich): ?>
                <div class="sandwich-item">
                    <h2><?= htmlspecialchars($sandwich['nom']) ?></h2>
                    <img src="images/sandwiches/<?= htmlspecialchars($sandwich['nom']) ?>.jpg" alt="<?= htmlspecialchars($sandwich['nom']) ?>" class="sandwich-image">
                    <p><?= htmlspecialchars($sandwich['description']) ?></p>
                    <p><strong>Prix :</strong> <?= number_format($sandwich['prix'], 2) ?> €</p>
                    <p><strong>Stock :</strong> <?= (int)$sandwich['stock'] ?></p>
                    <p>
                        <form action="" method="post">
                            <input type="hidden" name="sandwich_id" value="<?= $sandwich['id_sandwichs'] ?>">
                            <input type="hidden" name="sandwich_stock" value="<?= $sandwich['stock'] ?>">
                            <button type="submit">Ajouter au panier</button>
                        </form>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer class="footer">
        <p>2025 épi doré. Réalisé par Elvin Mouyart</p>
        <p>Contact: <a href="mailto:mouyelv@sjb-liege.org">ici par mail !</a></p>
    </footer>
</body>
</html>
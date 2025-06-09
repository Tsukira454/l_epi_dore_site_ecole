<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Épi Doré</title>
    <link href="https://fonts.googleapis.com/css2?family=Parisienne&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Open Sans', sans-serif;
            background: #fff8f2;
            color: #4e3c2c;
        }

        header {
            background: #f8e1d4;
            padding: 20px;
            border-bottom: 2px solid #e0c7b4;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Parisienne', cursive;
            font-size: 2.2em;
            margin-left: 20px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-right: 20px;
        }

        .nav-links a,
        .nav-links button {
            background: #d18b67;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
        }

        .nav-links a:hover {
            background: #b66c49;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: #d18b67;
        }

        .cart-button {
            position: relative;
            display: inline-block;
            font-size: 1em;
            background: #d18b67;
            padding: 10px 15px;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        .cart-button:hover {
            background: #b66c49;
        }

        .cart-button i {
            margin-right: 5px;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff4e4e;
            color: white;
            font-size: 0.75em;
            padding: 3px 7px;
            border-radius: 50%;
            font-weight: bold;
        }

        main {
            padding: 40px 20px;
            text-align: center;
        }

        h1 {
            font-family: 'Parisienne', cursive;
            font-size: 3em;
            margin-bottom: 30px;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            border-radius: 15px;
            max-height: 160px;
            object-fit: cover;
        }

        .card h3 {
            margin-top: 15px;
            font-size: 1.3em;
        }

        .card p {
            font-size: 0.95em;
            margin: 10px 0;
        }

        .add-to-cart {
            background: #d18b67;
            border: none;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
            font-size: 0.9em;
        }

        .add-to-cart i {
            margin-right: 5px;
        }

        .add-to-cart:hover {
            background: #b66c49;
        }

        #cart-preview {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #fff5ed;
            border: 1px solid #d18b67;
            padding: 15px 20px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            max-width: 250px;
            display: none;
        }

        #cart-preview.show {
            display: block;
            animation: fadeInOut 3.5s forwards;
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(10px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>

<header>
    <nav>
        <div class="logo">Épi Doré</div>
        <div class="nav-links">
            <?php if (isset($_SESSION['email'])): ?>
                <div class="user-info">
                    <div class="user-icon">👤</div>
                    <span><?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></span>
                    <a href="commande.php">Nouvelle Commande</a>
                </div>
            <?php else: ?>
                <a href="php/login.php">Connexion</a>
                <a href="php/inscription.php">Inscription</a>
            <?php endif; ?>

            <?php $count = array_sum($_SESSION['panier'] ?? []); ?>
            <a href="php/panier.php" class="cart-button">
                <i class="fas fa-shopping-cart"></i> Panier
                <?php if ($count > 0): ?>
                    <span class="cart-count"><?= $count ?></span>
                <?php endif; ?>
            </a>
        </div>
    </nav>
</header>

<?php require(__DIR__ . "/php/menu.php"); ?>

<main>
    <?php if (isset($_SESSION['email'])): ?>
        <h1>Bienvenue <?= htmlspecialchars($_SESSION['prenom']) ?> !</h1>
    <?php else: ?>
        <h1>Bienvenue sur le site d'Épi Doré</h1>
    <?php endif; ?>

    <section class="products">
        <?php
        require(__DIR__ . "/php/dbConnect.php");
        $stmt = $dbEpidore->query("SELECT * FROM produits");
        while ($produit = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="card">';
            //echo '<img src="images/' . strtolower($produit["nom"]) . '.jpg" alt="' . htmlspecialchars($produit["nom"]) . '">';
            echo '<h3>' . htmlspecialchars($produit["nom"]) . '</h3>';
            echo '<p>' . htmlspecialchars($produit["description"]) . '</p>';
            echo '<p><strong>' . $produit["prix"] . ' €</strong></p>';
            echo '<form method="POST" action="php/ajouter_panier.php">';
            echo '<input type="hidden" name="id_produit" value="' . $produit["id_produits"] . '">';
            echo '<button type="submit" class="add-to-cart"><i class="fas fa-cart-plus"></i> Ajouter</button>';
            echo '</form>';
            echo '</div>';
        }
        ?>
    </section>
</main>

<div id="cart-preview"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('added') === 'true') {
        fetch("php/mini_panier.php")
            .then(res => res.text())
            .then(html => {
                const preview = document.getElementById("cart-preview");
                preview.innerHTML = html;
                preview.classList.add("show");
                setTimeout(() => {
                    preview.classList.remove("show");
                }, 3500);
            });
    }
});
</script>

</body>
</html>

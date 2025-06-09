<?php
session_start();
require(__DIR__ . "/dbConnect.php");

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$panier = $_SESSION['panier'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_produit'];

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'increment':
                $_SESSION['panier'][$id]++;
                break;
            case 'decrement':
                if ($_SESSION['panier'][$id] > 1) {
                    $_SESSION['panier'][$id]--;
                } else {
                    unset($_SESSION['panier'][$id]);
                }
                break;
            case 'remove':
                unset($_SESSION['panier'][$id]);
                break;
        }
    }

    header("Location: panier.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Panier - Épi Doré</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff8f2;
            padding: 40px;
            color: #4e3c2c;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
        }

        .panier {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .item:last-child {
            border-bottom: none;
        }

        .nom {
            flex: 1;
        }

        .quantite {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            padding: 6px 12px;
            background-color: #d18b67;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 0.9em;
        }

        .btn:hover {
            background-color: #b66c49;
        }

        .total {
            margin-top: 25px;
            text-align: right;
            font-size: 1.3em;
            font-weight: bold;
        }

        .vide {
            text-align: center;
            font-size: 1.2em;
        }
    </style>
</head>
<body>

<h1>Votre panier</h1>

<div class="panier">
    <?php if (empty($panier)): ?>
        <p class="vide">Votre panier est vide.</p>
    <?php else: ?>
        <?php
        $total = 0;
        foreach ($panier as $id => $quantite):
            $stmt = $dbEpidore->prepare("SELECT * FROM produits WHERE id_produits = ?");
            $stmt->execute([$id]);
            $produit = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$produit) continue;
            $sousTotal = $produit['prix'] * $quantite;
            $total += $sousTotal;
        ?>
            <div class="item">
                <div class="nom"><?= htmlspecialchars($produit['nom']) ?> - <?= number_format($produit['prix'], 2) ?>€</div>
                <div class="quantite">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_produit" value="<?= $id ?>">
                        <input type="hidden" name="action" value="decrement">
                        <button class="btn">-1</button>
                    </form>

                    <span><strong><?= $quantite ?></strong></span>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_produit" value="<?= $id ?>">
                        <input type="hidden" name="action" value="increment">
                        <button class="btn">+1</button>
                    </form>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_produit" value="<?= $id ?>">
                        <input type="hidden" name="action" value="remove">
                        <button class="btn"><i class="fas fa-times"></i></button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="total">
            Total : <?= number_format($total, 2) ?> €
        </div>
    <?php endif; ?>
</div>

</body>
</html>

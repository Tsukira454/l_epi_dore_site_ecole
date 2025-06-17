<?php 
session_start();
if (!isset($_SESSION['userID'])) {
    header('Location: ../index.php');
    exit();
}
require_once(__DIR__ . "/dbConnect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['nom'])) {
    $nom = $_POST['nom'];
    $action = $_POST['action'];

    // Récupérer l'ID du produit à partir de son nom
    $requete_id = $dbEpidore->prepare("SELECT id_sandwichs FROM sandwichs WHERE nom = :nom LIMIT 1");
    $requete_id->execute([':nom' => $nom]);
    $sandwich = $requete_id->fetch();

    if ($sandwich) {
        $idProduit = $sandwich['id_sandwichs'];
        $idClient = $_SESSION['userID'];

        switch ($action) {
            case 'plus':
                $requete_stock = $dbEpidore->prepare("SELECT stock FROM sandwichs WHERE id_sandwichs = :id");
                $requete_stock->execute([':id' => $idProduit]);
                $stock = $requete_stock->fetchColumn();

                if ($stock > 0) {
                    $requete_plus = $dbEpidore->prepare("UPDATE ligne_commande SET quantite = quantite + 1 WHERE fk_produit = :id AND fk_client = :client");
                    $requete_plus->execute([':id' => $idProduit, ':client' => $idClient]);

                    $requete_update_stock = $dbEpidore->prepare("UPDATE sandwichs SET stock = stock - 1 WHERE id_sandwichs = :id");
                    $requete_update_stock->execute([':id' => $idProduit]);
                } else {
                    $errorMessage = "Le sandwich est en rupture de stock.";
                }
                break;

            case 'moins':
                // Vérifier la quantité actuelle
                $requete_quantite = $dbEpidore->prepare("SELECT quantite FROM ligne_commande WHERE fk_produit = :id AND fk_client = :client");
                $requete_quantite->execute([':id' => $idProduit, ':client' => $idClient]);
                $quantite = $requete_quantite->fetchColumn();

                if ($quantite > 1) {
                    $requete_moins = $dbEpidore->prepare("UPDATE ligne_commande SET quantite = quantite - 1 WHERE fk_produit = :id AND fk_client = :client");
                    $requete_moins->execute([':id' => $idProduit, ':client' => $idClient]);

                    $requete_update_stock = $dbEpidore->prepare("UPDATE sandwichs SET stock = stock + 1 WHERE id_sandwichs = :id");
                    $requete_update_stock->execute([':id' => $idProduit]);
                }
                break;

            case 'supprimer':
                $requete_quantite = $dbEpidore->prepare("SELECT quantite FROM ligne_commande WHERE fk_produit = :id AND fk_client = :client");
                $requete_quantite->execute([':id' => $idProduit, ':client' => $idClient]);
                $quantite = $requete_quantite->fetchColumn();

                if ($quantite !== false) {
                    $requete_update_stock = $dbEpidore->prepare("UPDATE sandwichs SET stock = stock + :quantite WHERE id_sandwichs = :id");
                    $requete_update_stock->execute([':quantite' => $quantite, ':id' => $idProduit]);

                    $requete_delete = $dbEpidore->prepare("DELETE FROM ligne_commande WHERE fk_produit = :id AND fk_client = :client");
                    $requete_delete->execute([':id' => $idProduit, ':client' => $idClient]);
                }
                break;
        }

        // Rafraîchir la page pour éviter le resubmit
        header('Location: ./panier.php');
        exit();
    }
}
$request = $dbEpidore->prepare("
    SELECT s.nom, s.prix, l.quantite
    FROM ligne_commande l
    JOIN sandwichs s ON l.fk_produit = s.id_sandwichs
    WHERE l.fk_client = :id_clients
");
$request->execute([":id_clients" => $_SESSION['userID']]);
$results = $request->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/panier.css">
    <title>épi doré | Panier</title>
</head>
<body>
    <a href="../index.php" class="home-icon" aria-label="Accueil">
        <img src="../images/house.png" alt="Accueil" width="30" height="30">
    </a>
    <div class="main_form">
        <h1>Mon Panier :</h1>
        <?php if (empty($results)): ?>
            <p>Votre panier est vide.</p>
        <?php endif; ?>
        <form method="post" action="">
            <div class="panier-container">
                <?php foreach ($results as $panier): ?>
                    <div class="panier-item">
                        <form method="post" action="">
                            <p>Sandwich : <?php echo htmlspecialchars($panier['nom']); ?></p>
                            <p>Quantité : <?php echo htmlspecialchars($panier['quantite']); ?></p>
                            <p>Prix unitaire : <?php echo htmlspecialchars($panier['prix']); ?> €</p>
                            <div class="prix">
                                <input type="hidden" name="nom" value="<?php echo htmlspecialchars($panier['nom']); ?>">
                                <button type="submit" name="action" value="moins" class="btn-moins">-</button>
                                <p><?php echo ($panier['prix'] * $panier['quantite']); ?> €</p>
                                <button type="submit" name="action" value="plus" class="btn-plus">+</button>
                                <button type="submit" name="action" value="supprimer" class="btn-supprimer">X</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="total">
                <p>Total : 
                    <?php $total=0; foreach ($results as $panier) {
                        $total += $panier['prix'] * $panier['quantite'];
                    }
                    echo ($total); ?> €
                </p>
            </div>
            <div class="payment-options">
                <div class="payment-option">
                    <input type="radio" name="payment-method" value="credit-card" id="credit-card" checked>
                    <label for="credit-card">
                        <img src="../images/Payconiq.png" alt="Carte de crédit">

                    </label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="payment-method" value="paypal" id="paypal">
                    <label for="paypal">
                        <img src="../images/paypal.png" alt="PayPal">
                    </label>
                </div>
            </div>
            <div class="btn">
                <input type="hidden" name="total" value="<?php echo $total; ?>">
                <button type="submit">Payé</button>
            </div>
        </form>
        <div class="error">
            <?php if(isset($errorMessage)) {echo "<p style='color: red;'>$errorMessage</p>";}?>
        </div>
    </div>
</body>
</html>
<?php 
session_start();
if (!isset($_SESSION['userID'])) {
    header('Location: ../index.php');
    exit();
}
require_once(__DIR__ . "/dbConnect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if (
        $action == 'payement' &&
        isset($_POST['total'], $_POST['adresse_rue'], $_POST['adresse_ville'], $_POST['adresse_cp'], $_POST['adresse_pays'], $_POST['politique'], $_POST['payement'])
    ) {
        $total = $_POST['total'];
        $adresse = [
            'rue' => $_POST['adresse_rue'],
            'ville' => $_POST['adresse_ville'],
            'cp' => $_POST['adresse_cp'],
            'pays' => $_POST['adresse_pays']
        ];
        $politique = $_POST['politique'];
        $payement = $_POST['payement'];
        if ($politique !== 'accepte' || $payement !== 'accepte') {
            $errorMessage = "Vous devez accepter les conditions générales de vente et le mode de paiement.";
        } else {
            $requete_supp_panier = $dbEpidore->prepare("DELETE FROM ligne_commande WHERE fk_client = :id_clients");
            $requete_supp_panier->execute([':id_clients' => $idClient = $_SESSION['userID']]);
            $email_to = $_SESSION['email'];
            $email_subject = "Confirmation de commande";
            $email_message = "Votre commande a été confirmée. Détails :\nTotal : $total €\nAdresse : " . implode(", ", $adresse);
            mail($email_to, $email_subject, $email_message);
            $errorMessage = "Votre commande a été confirmée. Un email de confirmation a été envoyé à " . $_SESSION['email'] . ".";
        }
        //exit();
    }
    else if(isset($_POST['nom'])) {
        $nom = $_POST['nom'];

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
    }
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
                    <input type="radio" name="payment-method" value="Payconiq" id="credit-card" checked>
                    <label for="credit-card">
                        <img src="../images/Payconiq.png" alt="Carte de crédit">
                    </label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="payment-method" value="PayPal" id="paypal">
                    <label for="paypal">
                        <img src="../images/paypal.png" alt="PayPal">
                    </label>
                </div>
            </div>
        </form>
        <div class="btn">
            <form method="post" action="">
                <input type="hidden" name="total" value="<?php echo $total; ?>">

                <div class="checkbox-group">
                    <div class="adresse-group">
                        <p>Veuillez entrer l'adresse de livraison complète :</p>
                        <input type="text" name="adresse_rue" placeholder="Rue et numéro" required>
                        <input type="text" name="adresse_ville" placeholder="Ville" required>
                        <input type="text" name="adresse_cp" placeholder="Code Postal" pattern="\d{4,5}"
                            title="Code postal à 4 ou 5 chiffres" required>
                        <input type="text" name="adresse_pays" placeholder="Pays" required>
                    </div>

                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="politique" value="accepte" id="politique" required>
                    <label for="politique">J'accepte les <a href="../php/conditions_generales.php"
                            target="_blank">conditions générales de vente</a></label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="payement" value="accepte" id="paiement" required>
                    <label for="paiement">Je confirme vouloir payer avec <span id="selected-method">Payconiq</span> dès
                        que je clique sur "payé"</label>
                </div>

                <button type="submit" name="action" value="payement">Payé</button>
            </form>
        </div>
        <div class="error">
            <?php if(isset($errorMessage)) {echo "<p style='color: red;'>$errorMessage</p>";}?>
        </div>
    </div>
    <script>
    const radios = document.querySelectorAll('input[name="payment-method"]');
    const methodSpan = document.getElementById('selected-method');

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            methodSpan.textContent = radio.value;
        });
    });
    </script>
</body>

</html>
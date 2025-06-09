<?php
session_start();

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$id_produit = $_POST['id_produit'] ?? null;

if ($id_produit !== null) {
    if (!isset($_SESSION['panier'][$id_produit])) {
        $_SESSION['panier'][$id_produit] = 1;
    } else {
        $_SESSION['panier'][$id_produit]++;
    }
}

header("Location: ../index.php");
exit;

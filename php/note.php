<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <title>épi doré | Note</title>
</head>
<body>
    <a href="../index.php" class="home-icon" aria-label="Accueil">
        <img src="../images/house.png" alt="Accueil" width="30" height="30">
    </a>
    <div class="main_form">
        <h1>Note au professeur</h1>
        <form action="" method="post">
            <div class="main">
                <p>
                    Bonjour,<br>
                    Je suis Elvin Mouyart de 5(T)b.<br><br>
                    Je vous ai mis des petit note pour vous aidé a vous repéré car devoir corrigé plusieur site en même temps n'est pas facile.<br>
                    Donc voici plusieur points a savoir pour bien comprendre le site :<br><br>
                    <ul>
                        <li>Le site empêche tout accès au connection inscription ou profil selon si l'utilisateur est connecté ou pas.</li>
                    </ul>
                    <br>
                    Donc voila, j'espère que vous trouverez ce site agréable et facile à utiliser.<br>
                </p>
            </div>
            <div class="btn">
                <button type="submit">Ok !</button>
            </div>
        </form>
    </div>
</body>
</html>

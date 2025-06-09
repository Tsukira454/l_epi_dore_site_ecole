<?php 
session_start();
if (isset($_SESSION['userID'])) {
    header('Location: ../index.php');
    exit();
}
if(isset($_POST['mail'])) {
    $email = htmlspecialchars($_POST['mail']);
    $password = hash('sha256', htmlspecialchars($_POST['mdp']));

    require_once(__DIR__ . "/dbConnect.php");
    $request = $dbEpidore->prepare("SELECT * FROM clients WHERE email = :email AND mdp = :mdp");
    $request->execute(array(
        ":email" => $email,
        ":mdp" => $password
    ));
    $results = $request->fetchAll();

    if(count($results) == 1) {
        $_SESSION['userID'] = $results[0]['id_clients'];
        $_SESSION['email'] = $results[0]['email'];
        $_SESSION['nom'] = $results[0]['nom'];
        $_SESSION['prenom'] = $results[0]['prenom'];
        $_SESSION['tel'] = $results[0]['tel'];
        header('Location: ../index.php');
        exit();
    } else {
        $errorMessage = "Mauvaise combinaison de login et mot de passe.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main_log_reg.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <title>épi doré | login</title>
</head>
<body>
    <a href="../index.php" class="home-icon" aria-label="Accueil">
        <img src="../images/house.png" alt="Accueil" width="30" height="30">
    </a>
    <div class="main_form">
        <form method="post" action="">
            <div class="main">

                <label for="mail">email :</label>
                <input type="email" id="mail" name="mail" required>
                
                <label for="mdp">Mot de passe:</label>
                <input type="password" id="mdp" name="mdp" required>
            </div>
            <div class="btn">
                <button type="submit">Se connecté</button>
            </div>
            <div class="link">
                <p>Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous</a></p>
            </div>
        </form>
        <div class="error">
            <?php 
            if(isset($errorMessage)) {
                echo "<p style='color: red;'>$errorMessage</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
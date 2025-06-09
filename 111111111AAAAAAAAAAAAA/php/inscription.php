<?php
if(isset($_POST['nom'])) {
    // Valider le formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $telephone = htmlspecialchars($_POST['tel']);
    $email = htmlspecialchars($_POST['mail']);
    $password = hash('sha256', htmlspecialchars($_POST['mdp']));

    try {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Email invalide");
        require_once(__DIR__ . "/dbConnect.php");
        
        // Créer l'utilisateur
        $request = $dbEpidore->prepare("INSERT INTO clients (prenom, nom, email, tel, mdp) VALUES(:prenom, :nom, :email, :tel, :mdp)");
        $request->execute(array(
            ":prenom" => $prenom,
            ":nom" => $nom,
            ":email" => $email,
            ":tel" => $telephone,
            ":mdp" => $password
        ));
        
        // Redirection après inscription réussie
        header('Location: login.php');
        exit();
    } catch(Exception $e) {
        $errorMessage = "Erreur lors de l'inscription, veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Epi Dore</title>
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.2.0/css/all.css'>
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.2.0/css/fontawesome.css'>
    <link rel="stylesheet" href="../css/style_registeur.css">
    <link rel="icon" href="../images/logo.png" sizes="32x32" type="image/png">

</head>
<body>
<div class="container">
    <div class="screen">
        <div class="screen__content">
            <form class="registeur" method="post" action="">
                <div class="registeur__field">
                    <i class="registeur__icon fas fa-user"></i>
                    <input type="text" class="registeur__input" name="nom" placeholder="Nom" required value="<?php echo $_POST['nom'] ?? '';?>">
                </div>
                <div class="registeur__field">
                    <i class="registeur__icon fas fa-user"></i>
                    <input type="text" class="registeur__input" name="prenom" placeholder="Prénom" required value="<?php echo $_POST['prenom'] ?? '';?>">
                </div>
                <div class="registeur__field">
                    <i class="registeur__icon fas fa-phone"></i>
                    <input type="text" class="registeur__input" name="tel" placeholder="Téléphone" required value="<?php echo $_POST['tel'] ?? '';?>">
                </div>
                <div class="registeur__field">
                    <i class="registeur__icon fas fa-envelope"></i>
                    <input type="email" class="registeur__input" name="mail" placeholder="Email" required value="<?php echo $_POST['mail'] ?? '';?>">
                </div>
                <div class="registeur__field">
                    <i class="registeur__icon fas fa-lock"></i>
                    <input type="password" class="registeur__input" name="mdp" placeholder="Mot de passe" required>
                </div>
                <?php if(isset($errorMessage)) echo "<p class='error'>$errorMessage</p>"; ?>
                <button class="button registeur__submit" type="submit">
                    <span class="button__text">S'inscrire</span>
                    <i class="button__icon fas fa-chevron-right"></i>
                </button>
            </form>
        </div>
        <div class="screen__background">
            <span class="screen__background__shape screen__background__shape4"></span>
            <span class="screen__background__shape screen__background__shape3"></span>
            <span class="screen__background__shape screen__background__shape2"></span>
            <span class="screen__background__shape screen__background__shape1"></span>
        </div>
    </div>
</div>
</body>
</html>

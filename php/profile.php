<?php 
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: connection.php");
    exit();
}

if(isset($_POST['mdp'])) {
    // Valider le formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $telephone = htmlspecialchars($_POST['tel']);
    $email = htmlspecialchars($_POST['mail']);
    $password = hash('sha256', htmlspecialchars($_POST['mdp']));

    try {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Email invalide");
        require_once(__DIR__ . "/dbConnect.php");
        
        $request = $dbEpidore->prepare("UPDATE clients SET prenom = :prenom, nom = :nom, email = :email, tel = :tel, mdp = :mdp WHERE id_clients = :id");
        $request->execute(array(
            ":prenom" => $prenom,
            ":nom" => $nom,
            ":email" => $email,
            ":tel" => $telephone,
            ":mdp" => $password,
            ":id" => $_SESSION['userID']
        ));

        $_SESSION['email'] = $email;
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['tel'] = $telephone;
        header('Location: ../index.php');
        exit();
    } catch(Exception $e) {
        //$errorMessage = "Erreur lors de l'inscription, veuillez réessayer.";
        $errorMessage = $e;
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
    <title>épi doré | Profil</title>
</head>
<body>
    <a href="../index.php" class="home-icon" aria-label="Accueil">
        <img src="../images/house.png" alt="Accueil" width="30" height="30">
    </a>
    <div class="main_form">
        <form method="post" action="">
            <h1>Mon Profil</h1>
            <div class="main">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?php echo $_SESSION['nom']?>" required>

                <label for="prenom">prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo $_SESSION['prenom']?>" required>

                <label for="mail">email :</label>
                <input type="email" id="mail" name="mail" value="<?php echo $_SESSION['email']?>" required>

                <label for="tel">téléphone :</label>
                <input type="tel" id="tel" name="tel" value="<?php echo $_SESSION['tel']?>" required>
                
                <label for="mdp">Mot de passe:</label>
                <input type="password" id="mdp" name="mdp" required>
            </div>
            <div class="btn">
                <button type="submit">Modifier mon profil</button>
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
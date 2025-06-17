<?php 
session_start();
if (isset($_SESSION['userID'])) {
    header('Location: ../index.php');
    exit();
}

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
        
        $request = $dbEpidore->prepare("SELECT id FROM clients WHERE email = :email");
        $request->execute([":email" => $email]);
        if ($request->rowCount() > 0) {
            $errorMessage = "Un compte avec cet email existe déjà.";
        } else {
        $request = $dbEpidore->prepare("INSERT INTO clients (prenom, nom, email, tel, mdp) VALUES(:prenom, :nom, :email, :tel, :mdp)");
        $request->execute(array(
            ":prenom" => $prenom,
            ":nom" => $nom,
            ":email" => $email,
            ":tel" => $telephone,
            ":mdp" => $password,
        ));
        
        header('Location: connection.php');
        exit();
        }
    } catch(Exception $e) {
        $errorMessage = "Erreur lors de l'inscription, veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="icon" href="../images/logo.png" type="image/png">
    <title>épi doré | inscription</title>
</head>
<body>
    <a href="../index.php" class="home-icon" aria-label="Accueil">
        <img src="../images/house.png" alt="Accueil" width="30" height="30">
    </a>

    <div class="main_form">
        <form method="post" action="">
            <div class="main">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>

                <label for="prenom">prénom :</label>
                <input type="text" id="prenom" name="prenom" required>

                <label for="mail">email :</label>
                <input type="email" id="mail" name="mail" required>

                <label for="tel">téléphone :</label>
                <input type="tel" id="tel" name="tel" required>
                
                <label for="mdp">Mot de passe:</label>
                <input type="password" id="mdp" name="mdp" required>
            </div>
            <div class="btn">
                <button type="submit">S'inscrire</button>
            </div>
            <div class="link">
                <p>Déjà inscrit ? <a href="connection.php">Se connecter</a></p>
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
<?php
session_start();

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
        $_SESSION["userID"] = $results[0]['id'];
        $_SESSION["email"] = $email;
        $_SESSION['nom'] = $results[0]['nom'];
        $_SESSION['prenom'] = $results[0]['prenom'];
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
  <title>Login - Epi Dore</title>
  <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.2.0/css/all.css'>
  <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.2.0/css/fontawesome.css'>
  <link rel="stylesheet" href="../css/style_login.css">
  <link rel="icon" href="../images/logo.png" sizes="32x32" type="image/png">
</head>
<body>
<div class="container">
    <div class="screen">
        <div class="screen__content">
            <form class="login" method="post" action="">
                <div class="login__field">
                    <i class="login__icon fas fa-user"></i>
                    <input type="email" class="login__input" name="mail" placeholder="User name / Email" required value="<?php echo $_POST['mail'] ?? ''; ?>">
                </div>
                <div class="login__field">
                    <i class="login__icon fas fa-lock"></i>
                    <input type="password" class="login__input" name="mdp" placeholder="Password" required>
                </div>
                <?php if(isset($errorMessage)) echo "<p class='error'>$errorMessage</p>"; ?>
                <button class="button login__submit" type="submit">
                    <span class="button__text">Log In Now</span>
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

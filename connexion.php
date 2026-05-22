<?php
error_reporting(1);
session_start();
include("dbcon.php");



if (isset($_POST['connexion'])) {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur='$nom_utilisateur' AND mot_de_passe='$mot_de_passe'";
    $resultat = mysqli_query($con, $sql);

    if (mysqli_num_rows($resultat) == 1) {
        $_SESSION['user_session'] = $nom_utilisateur;
        $numero_facture = "RS-" . generer_numero_facture();
        header("Location: accueil.php?numero_facture=$numero_facture");
        exit;
    } else {
        $message_erreur = "<center><font color='red'>Nom d'utilisateur ou mot de passe incorrect</font></center>";
    }
}

function generer_numero_facture() {
    $caracteres = "09302909209300923";
    srand((double)microtime() * 1000000);
    $facture = '';

    for ($i = 0; $i < 7; $i++) {
        $numero = rand() % 10;
        $facture .= substr($caracteres, $numero, 1);
    }
    return $facture;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Système de Gestion de Pharmacie</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap-responsive.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <script src="js/jquery-1.7.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body style="background:url('images/fresh_snow.png')">

    <center>
        <h1>PHARMAGEST</h1>
    </center>

    <div class="content" style="width: 500px; margin: auto;">

        <form method="POST">

            <table class="table table-bordered table-responsive">
                <tr>
                    <td><label for="nom_utilisateur">Nom d'utilisateur</label></td>
                    <td><input type="text" name="nom_utilisateur" class="form-control" required></td>
                </tr>
                <tr>
                    <td><label for="mot_de_passe">Mot de passe</label></td>
                    <td><input type="password" name="mot_de_passe" class="form-control" required></td>
                </tr>
            </table>

            <input type="submit" name="connexion" class="btn btn-primary btn-large" value="Se connecter">

            <?php if (isset($message_erreur)) echo $message_erreur; ?>

        </form>
        <br>
        <center>
            <p>Pas encore de compte ? <a href="inscription.php">S'inscrire ici</a></p>
        </center>
    </div>
</body>
</html>

<?php
include("dbcon.php");

if (isset($_POST['inscription'])) {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $verif_sql = "SELECT * FROM utilisateurs WHERE nom_utilisateur = '$nom_utilisateur'";
    $verif_resultat = mysqli_query($con, $verif_sql);

    if (mysqli_num_rows($verif_resultat) > 0) {
        $message = "<center><font color='red'>Ce nom d'utilisateur existe déjà !</font></center>";
    } else {
        $insert_sql = "INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe) VALUES ('$nom_utilisateur', '$mot_de_passe')";
        if (mysqli_query($con, $insert_sql)) {
            $message = "<center><font color='green'>Inscription réussie. <a href='connexion.php'>Connectez-vous</a></font></center>";
        } else {
            $message = "<center><font color='red'>Erreur lors de l'inscription.</font></center>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - PHARMAGEST</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body style="background:url('images/fresh_snow.png')">

    <center>
        <h1>Inscription à PHARMAGEST</h1>
    </center>

    <div class="content" style="width: 500px; margin: auto;">

        <form method="POST">
            <table class="table table-bordered">
                <tr>
                    <td><label for="nom_utilisateur">Nom d'utilisateur</label></td>
                    <td><input type="text" name="nom_utilisateur" class="form-control" required></td>
                </tr>
                <tr>
                    <td><label for="mot_de_passe">Mot de passe</label></td>
                    <td><input type="password" name="mot_de_passe" class="form-control" required></td>
                </tr>
            </table>

            <input type="submit" name="inscription" class="btn btn-success" value="S'inscrire">

            <?php if (isset($message)) echo $message; ?>
        </form>

        <br>
        <center>
            <p>Déjà un compte ? <a href="connexion.php">Connectez-vous ici</a></p>
        </center>
    </div>
</body>
</html>

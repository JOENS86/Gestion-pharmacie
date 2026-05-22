<?php

session_start();

if(!isset($_SESSION['user_session'])){
    header("location:index.php");
}
?>
<html>
<head>
    <title>Paiement</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="checkout">
    <form method="post" action="preview.php?numero_facture=<?php echo $_GET['numero_facture'] ?>">
        <center>
            <input type="hidden" name="nom_medicament" value="<?php echo $_GET['nom_medicament'] ?>">
            <input type="hidden" name="categorie" value="<?php echo $_GET['categorie'] ?>">
            <input type="hidden" name="quantite" value="<?php echo $_GET['quantite'] ?>">
            <input type="hidden" name="montant_total" value="<?php echo $_GET['total'] ?>">
            <input type="hidden" name="benefice_total" value="<?php echo $_GET['profit'] ?>">
            <input type="hidden" name="date" value="<?php echo date("Y/m/d"); ?>">

            <input type="number" name="montant_paye" autocomplete="off" placeholder="Montant payé" style="width: 300px; height:30px; margin-bottom: 15px;" required/><br>

            <button class="btn btn-success btn-block btn-large" name="valider">Valider</button>
        </center>
    </form>
</div>
</body>
</html>

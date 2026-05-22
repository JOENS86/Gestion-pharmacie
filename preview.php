<?php
session_start();

if(!isset($_SESSION['session_utilisateur'])){
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Facture</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/tcal.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/tcal.js"></script>
    <script type="text/javascript">
        function imprimerFacture() { 
            var options = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes,width=700,height=400,left=100,top=25"; 
            var contenu = document.getElementById("contenu").innerHTML; 
            var fenetre = window.open("", "", options); 
            fenetre.document.open(); 
            fenetre.document.write('</head><body onLoad="self.print()" style="width: 700px; font-size:11px; font-family:arial;">');          
            fenetre.document.write(contenu); 
            fenetre.document.close(); 
            fenetre.focus(); 
        }
    </script>
</head>
<body style="background-image: url('images/old_moon.png');">

<div class="container">

    <a href="accueil.php?numero_facture=<?php echo $_GET['numero_facture']?>"><button class="btn btn-default"><i class="icon-arrow-left"></i> Retour à la vente</button></a>

    <div id="contenu">
        <center><div style="font:bold 25px 'Arial';">Pharmagest</div><br></center><br><br>

        <?php 
        $numero_facture = $_GET['numero_facture'];
        $date = $_POST['date'];
        $montant_paye = $_POST['montant_paye'];
        ?>

        <form method="POST" action="enregistrer_facture.php">
            <table border="1" cellpadding="4" cellspacing="0" style="font-family: arial; font-size: 12px;text-align:left;" width="100%">
                <tr>
                    <strong><h3>Facture n° : <?php echo $numero_facture?></h3></strong> 
                    <?php echo $date ?>
                </tr>
                <thead>
                    <tr>
                        <th>Nom du produit</th>
                        <th>Catégorie</th>
                        <th>Qté</th>
                        <th>Prix</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    include("connexion_bd.php");
                    $requete = "SELECT * FROM en_attente WHERE numero_facture = '$numero_facture'";
                    $resultat = mysqli_query($con, $requete);

                    while($ligne = mysqli_fetch_array($resultat)):
                ?>
                    <tr class="record">
                        <td><h4><?php echo $ligne['nom_medicament'];?></h4>
                            <input type="hidden" name="nom_medicament[]" value="<?php echo $ligne['nom_medicament']?>"></td>
                            <input type="hidden" name="date_expiration" value="<?php echo $ligne['date_expiration']?>">

                        <input type="hidden" name="categorie" value="<?php echo $ligne['categorie']?>">

                        <td><h5><?php echo $ligne['categorie']; ?></h5></td>
                        <td><h5><?php echo $ligne['quantite']." (".$ligne['type_vente'].")"; ?></h5>
                            <input type="hidden" name="quantite[]" value="<?php echo $ligne['quantite']."(".$ligne['type_vente'].")"; ?>">
                        </td>
                        <td><h5><?php echo $ligne['prix_vente']; ?></h5></td>
                        <td><h5><?php echo $ligne['montant']; ?></h5></td>
                    </tr>
                <?php endwhile; ?>

                <tr>
                    <td colspan="4" style="text-align:right;"><strong style="font-size: 12px;">Total :</strong></td>
                    <td colspan="2"><strong style="font-size: 12px;">
                        <?php
                            $result_total = mysqli_query($con, "SELECT SUM(montant) AS total FROM en_attente WHERE numero_facture = '$numero_facture'");
                            $total = mysqli_fetch_assoc($result_total)['total'];
                            echo '<h5>'.$total.'</h5>';
                        ?>
                    </strong></td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align:right;"><strong style="font-size: 12px;">Montant Payé :</strong></td>
                    <td colspan="2"><strong><h3><?php echo $montant_paye; ?></h3></strong></td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align:right;"><strong style="font-size: 18px;">Monnaie rendue :</strong></td>
                    <td colspan="2"><strong><h3><?php echo $montant_paye - $total; ?></h3></strong></td>
                </tr>

                </tbody>
            </table><br/>
    </div>

    <input type="hidden" name="montant_paye" value="<?php echo $montant_paye ?>">
    <input type="hidden" name="numero_facture" value="<?php echo $numero_facture ?>">
    <input type="hidden" name="date" value="<?php echo $date ?>">
    <input type="submit" name="soumettre" class="btn btn-success btn-large" value="Valider et Nouvelle Vente" >
    <a href="javascript:imprimerFacture()" class="btn btn-primary" style="float: right;"> Imprimer</a>

    </form>
</body>
</html>

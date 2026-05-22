<?php
session_start();

if(!isset($_SESSION['session_utilisateur'])){
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Médicaments en expiration</title>
    <script type="text/javascript">
        // Fonction de recherche par nom de médicament
        function recherche_nom_medicament() {
            var input, filtre, tableau, lignes, cellule, i;
            input = document.getElementById("champ_nom_medicament");
            filtre = input.value.toUpperCase();
            tableau = document.getElementById("tableau_medicaments");
            lignes = tableau.getElementsByTagName("tr");

            for (i = 0; i < lignes.length; i++) {
                cellule = lignes[i].getElementsByTagName("td")[0];
                if (cellule) {
                    if (cellule.innerHTML.toUpperCase().indexOf(filtre) > -1) {
                        lignes[i].style.display = "";
                    } else {
                        lignes[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</head>
<body>

<div class="expiration" style="color:green;">
    <font size="5">Médicaments Proches de l'Expiration</font><br><hr>
    <input type="text" id="champ_nom_medicament" size="4" onkeyup="recherche_nom_medicament()" placeholder="Rechercher un médicament..." title="Tapez un nom">

    <div style="overflow-x:auto; overflow-y:auto; height: 200px;">
        <table class="table table-bordered" id="tableau_medicaments">
            <tr>
                <th>Médicament</th>
                <th>Date d'expiration</th>
                <th>Qté restante</th>
                <th>Prix unitaire</th>
                <th>Coût total</th>
            </tr>

            <?php
            include("connexion_bd.php");

            $date_aujourdhui = date('d-m-Y');    
            $date_limite = date("Y-m-d", strtotime("+6 month", strtotime($date_aujourdhui))); 

            $requete = "SELECT * FROM stock WHERE date_expiration <= '$date_limite' AND statut = 'Disponible' ORDER BY date_expiration ASC";
            $resultat = mysqli_query($con, $requete); 

            while ($ligne = mysqli_fetch_array($resultat)):  
            ?> 
                <tr>
                    <td><?php echo $ligne['nom_medicament']; ?></td>
                    <td><font color="red"><?php echo $ligne['date_expiration']; ?></font></td>
                    <td><?php echo $ligne['quantite_restante']." (".$ligne['type_vente'].")"; ?></td>
                    <td><?php echo $ligne['prix_unitaire']; ?></td>
                    <td><?php echo $ligne['prix_unitaire'] * $ligne['quantite_restante']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>

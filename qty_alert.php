<?php
session_start();

if (!isset($_SESSION['user_session'])) {
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Médicaments en rupture</title>
    <!-- Lien vers Bootstrap pour le style -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    
    <script type="text/javascript">
    function rechercherMedicament() {
        var input = document.getElementById("recherche_medicament");
        var filtre = input.value.toUpperCase();
        var table = document.getElementById("table_medicaments");
        var lignes = table.getElementsByTagName("tr");

        for (var i = 1; i < lignes.length; i++) {
            var cellule = lignes[i].getElementsByTagName("td")[0];
            if (cellule) {
                var texte = cellule.textContent || cellule.innerText;
                lignes[i].style.display = texte.toUpperCase().indexOf(filtre) > -1 ? "" : "none";
            }
        }
    }
    </script>
</head>
<body style="padding: 20px; background-color: #f7f7f7;">

    <div class="container bg-white p-4 rounded shadow">
        <h3 class="text-primary">📉 Médicaments bientôt en rupture de stock</h3>
        <hr>

        <input type="text" class="form-control mb-3" id="recherche_medicament" onkeyup="rechercherMedicament()" placeholder="Rechercher un médicament...">

        <div style="overflow-x:auto; overflow-y:auto; max-height: 300px;">
            <table class="table table-bordered table-hover" id="table_medicaments">
                <thead class="thead-dark">
                    <tr>
                        <th>Nom du médicament</th>
                        <th>Quantité restante</th>
                        <th>Date d'expiration</th>
                        <th>Prix d'achat</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                include("dbcon.php");

                $seuil_rupture = 10;
                $sql = "SELECT * FROM stock WHERE quantite_restante <= '$seuil_rupture' AND status = 'Available'";
                $resultats = mysqli_query($con, $sql);

                if (mysqli_num_rows($resultats) > 0) {
                    while ($ligne = mysqli_fetch_array($resultats)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($ligne['nom_medicament']) . "</td>";
                        echo "<td><span class='text-danger'>" . $ligne['quantite_restante'] . " (" . $ligne['type_vente'] . ")</span></td>";
                        echo "<td>" . htmlspecialchars($ligne['date_expiration']) . "</td>";
                        echo "<td>" . htmlspecialchars($ligne['prix_achat']) . " FCFA</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center text-muted'>Aucun médicament proche de la rupture</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['user_session'])) {
    header("location:../accueil.php");
    exit(); // Toujours ajouter exit() après une redirection
}

// Inclure la connexion à la base de données UNE SEULE FOIS au début.
include_once("../dbcon.php");

// Vérifier que le numéro de facture est présent et le sécuriser
if (!isset($_GET['numero_facture']) || trim($_GET['numero_facture']) === '') {
    // Afficher une erreur claire si le paramètre est manquant
    die("Erreur : Le numéro de facture est manquant dans l'URL.");
}
// Sécuriser la variable pour l'affichage dans le HTML (prévention XSS)
$numero_facture_safe = htmlspecialchars($_GET['numero_facture']);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Pharmagest - Produits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-responsive.css">
    <link rel="stylesheet" type="text/css" href="../src/facebox.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../src/facebox.js"></script>
    <script type="text/javascript">
       jQuery(document).ready(function($) {
            $("a[id*=popup]").facebox({
              loadingImage : '../src/img/loading.gif',
              closeImage   : '../src/img/closelabel.png'
            });
        });
    </script>
</head>
<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner" style="background: #000;">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
          </a>
          <a class="brand" href="#"><b>Pharmagest</b></a>
          <div class="nav-collapse">
            <ul class="nav pull-right">
               <li>
                <?php 
                // --- Alerte de quantité faible (corrigée et sécurisée) ---
                $qty_alert_count = 0;
                // Vérifier si la connexion DB est valide
                if (isset($con) && $con instanceof mysqli) {
                    $qty_threshold = "5"; // Le seuil est maintenant une variable
                    $sql_qty_alert = "SELECT id FROM stock WHERE quantite_restante <= ? AND status='Available'";
                    
                    if ($stmt_qty = mysqli_prepare($con, $sql_qty_alert)) {
                        mysqli_stmt_bind_param($stmt_qty, "s", $qty_threshold);
                        mysqli_stmt_execute($stmt_qty);
                        mysqli_stmt_store_result($stmt_qty);
                        $qty_alert_count = mysqli_stmt_num_rows($stmt_qty);
                        mysqli_stmt_close($stmt_qty);
                    }
                }
                
                if ($qty_alert_count == 0) {
                    echo '<a href="#" class="notification label-inverse"><span class="icon-exclamation-sign icon-large"></span></a>';
                } else {
                    echo '<a href="../qty_alert.php" class="notification label-inverse" id="popup">
                            <span class="icon-exclamation-sign icon-large"></span>
                            <span class="badge">' . $qty_alert_count . '</span></a>';
                }
                ?> 
               </li>
               <li>
                <?php
                // --- Alerte d'expiration (corrigée et sécurisée) ---
                $exp_alert_count = 0;
                if (isset($con) && $con instanceof mysqli) {
                    $exp_threshold_date = date("Y-m-d", strtotime("+6 months"));
                    $sql_exp_alert = "SELECT id FROM stock WHERE date_expiration <= ? AND status='Available'";

                    if ($stmt_exp = mysqli_prepare($con, $sql_exp_alert)) {
                        mysqli_stmt_bind_param($stmt_exp, "s", $exp_threshold_date);
                        mysqli_stmt_execute($stmt_exp);
                        mysqli_stmt_store_result($stmt_exp);
                        $exp_alert_count = mysqli_stmt_num_rows($stmt_exp);
                        mysqli_stmt_close($stmt_exp);
                    }
                }

                if ($exp_alert_count == 0) {
                    echo '<a href="#" class="notification label-inverse"><span class="icon-bell icon-large"></span></a>';
                } else {
                    echo '<a href="../ex_alert.php" class="notification label-inverse" id="popup">
                            <span class="icon-bell icon-large"></span>
                            <span class="badge">' . $exp_alert_count . '</span></a>';
                }
                ?>
               </li>
               <!-- Utilisation de la variable sécurisée et de urlencode pour les paramètres d'URL -->
               <li><a href="../accueil.php?numero_facture=<?php echo urlencode($numero_facture_safe); ?>"><span class="icon-home"></span>Accueil</a></li>
               <li><a href="../sales_report.php?numero_facture=<?php echo urlencode($numero_facture_safe); ?>"><span class="icon-bar-chart"></span>Rapport des Ventes</a></li>
               <li><a href="../logout.php" class="link"><font color='red'><span class="icon-off"></span></font>Déconnexion</a></li>
            </ul>
          </div>
        </div>
      </div>
  </div><br><br>

     <div class="container">
      <div class="contentheader"><h1>Produits</h1></div><br>
        <input type="text" id="name_med1" size="20" onkeyup="med_name1()" placeholder="Rechercher par nom..." title="Entrez un nom">
        <input type="text" size="20" id="med_quantite" onkeyup="quanti()" placeholder="Rechercher par catégorie..." title="Entrez une catégorie">
        <input type="text" size="20" id="med_exp_date" onkeyup="exp_date()" placeholder="Rechercher par date d'exp..." title="Entrez une date">
        <input type="text" size="20" id="med_status" onkeyup="stat_search()" placeholder="Rechercher par statut..." title="Entrez un statut">
       <a href="index.php?numero_facture=<?php echo urlencode($numero_facture_safe); ?>" id="popup"><button class="btn-primary btn-large"><span class="icon-plus-sign icon-large"></span> Ajouter un Médicament</button></a>
       <form action="import_xls.php?numero_facture=<?php echo urlencode($numero_facture_safe); ?>" method="post" name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data" style="display: inline-block; vertical-align: top;">
            <div>
               <input type="file" name="file" id="file" accept=".xls,.xlsx" required>
               <button type="submit" class="btn-danger btn-large"><span class="icon-download icon-large"></span> Importer Fichier Excel</button>
            </div>
        </form>
    </div><br>

    <?php
       // --- Compteur total de médicaments (corrigé) ---
       $total_medicines = 0;
       if (isset($con) && $con instanceof mysqli) {
           $sql_count = "SELECT COUNT(id) AS total FROM stock";
           $result_count = mysqli_query($con, $sql_count);
           if ($result_count) {
               $row_count = mysqli_fetch_assoc($result_count);
               $total_medicines = $row_count['total'];
               mysqli_free_result($result_count);
           }
       }
    ?>
      <div style="text-align:center;">
        Total des Médicaments : <font color="green" style="font:bold 22px 'Aleo';">[<?php echo $total_medicines; ?>]</font>
      </div>
      
    <div class="container" style="overflow-x:auto; overflow-y: auto;">
    <form method="POST">
          <div style="overflow-x:auto; overflow-y: auto; height: 370px;">
          <table id="table0" class="table table-bordered" style="width: 100%; border-color: #000000;">
           <thead>
             <tr style="background-color: #002233; color: #FFFFFF;">
                 <th>Nom medicament</th>
                 <th>Categorie</th>
                 <th style="background-color: green;">Qté Enregistrée</th>
                 <th style="background-color: orange;">Qté Utilisée</th>
                 <th>Qté Restante</th>
                 <th style="background-color: green;">Date Enregistrement</th>
                 <th style="background-color: red;">Date Expiration</th>
                 <th>Remarque</th>     
                 <th>Prix Achat</th>
                 <th style="background-color: orange">Prix Vente</th>
                 <th style="background-color: green;">Profit</th>
                 <th>Statut</th>
                 <th>Action</th>
             </tr>
           </thead>
            <tbody>
                <?php
                // --- Affichage de la liste des produits (corrigé et sécurisé) ---
                if (isset($con) && $con instanceof mysqli) {
                    $sql_list = "SELECT id, nom_medicament, categorie, quantite, quantite_utilisee, quantite_restante, date_enregistrement, date_expiration, compagnie, vente_type, prix_achat, prix_vente, prix_profit, status FROM stock ORDER BY id DESC";
                    $result_list = mysqli_query($con, $sql_list);
                    
                    // Vérifier si la requête a réussi
                    if ($result_list) {
                        while ($row = mysqli_fetch_assoc($result_list)) :
                ?>
                            <tr style="background-color: #C0C0C0;">
                                <!-- Utilisation de htmlspecialchars pour sécuriser toutes les sorties -->
                                <td><?php echo htmlspecialchars($row['nom_medicament']); ?></td>
                                <td><?php echo htmlspecialchars($row['categorie']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantite']) . "&nbsp;&nbsp;(<strong><i>" . htmlspecialchars($row['vente_type']) . "</i></strong>)"; ?></td>              
                                <td><?php echo htmlspecialchars($row['quantite_utilisee']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantite_restante']); ?></td>
                                <td><?php echo htmlspecialchars(date("d-m-Y", strtotime($row['date_enregistrement']))); ?></td>
                                <td><?php echo htmlspecialchars(date("d-m-Y", strtotime($row['date_expiration']))); ?></td>
                                <td><?php echo htmlspecialchars($row['compagnie']); ?></td>
                                <td><?php echo htmlspecialchars($row['prix_achat']); ?></td>
                                <td><?php echo htmlspecialchars($row['prix_vente']); ?></td>
                                <td><?php echo htmlspecialchars($row['prix_profit']); ?></td>
                                <td>
                                    <?php 
                                    $status = $row['status'];
                                    if ($status == 'Available') {
                                        echo '<span class="label label-success">' . htmlspecialchars($status) . '</span>';
                                    } else {
                                        echo '<span class="label label-danger">' . htmlspecialchars($status) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a id="popup" href="update_view.php?id=<?php echo urlencode($row['id']); ?>&numero_facture=<?php echo urlencode($numero_facture_safe); ?>"><button type="button" class="btn btn-warning"><span class="icon-edit"></span></button></a>
                                    <button type="button" class="btn btn-danger delete" id="<?php echo htmlspecialchars($row['id']); ?>"><span class="icon-trash"></span></button>
                                </td>
                            </tr>
                <?php 
                        endwhile;
                        mysqli_free_result($result_list); // Libérer la mémoire
                    } else {
                        // Afficher une erreur si la requête principale échoue
                        echo "<tr><td colspan='13' class='text-center text-error'>Erreur lors du chargement des produits.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='13' class='text-center text-error'>Erreur de connexion à la base de données.</td></tr>";
                }
                ?>
            </tbody>
           </table>
         </div>
      </form> 
    </div>
</body>
</html>

<script type="text/javascript">
// Les fonctions de recherche JavaScript restent les mêmes
function med_name1() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("name_med1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table0");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) { // Commence à 1 pour ignorer l'en-tête
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.textContent.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function quanti() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("med_quantite");
  filter = input.value.toUpperCase();
  table = document.getElementById("table0");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      if (td.textContent.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function exp_date() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("med_exp_date");
  filter = input.value.toUpperCase();
  table = document.getElementById("table0");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[6];
    if (td) {
      if (td.textContent.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function stat_search() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("med_status");
  filter = input.value.toUpperCase();
  table = document.getElementById("table0");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[11];
    if (td) {
      if (td.textContent.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

// La fonction de suppression reste la même
$(".delete").click(function() {
  var element = $(this);
  var del_id = element.attr("id");
  var info = 'id=' + del_id;
  if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
    $.ajax({
      type: "GET",
      url: 'delete.php',
      data: info,
      success: function() {
        // Optionnel : supprimer la ligne du tableau sans recharger la page
        element.closest('tr').fadeOut(300, function() { $(this).remove(); });
        // ou recharger la page :
        // location.reload(true);
      },
      error: function() {
        alert("Une erreur est survenue lors de la suppression.");
      }
    });
  }
  return false;
});
</script>
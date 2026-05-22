<?php
session_start();

if (!isset($_SESSION['user_session'])) {
    header("location:connexion.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHS SYSTEM</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css">
    <link rel="stylesheet" href="css/jquery.css">
  <link rel="stylesheet" type="text/css" href="src/facebox.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <style type="text/css">

  </style>
    
    <script src="js/jquery-1.7.2.min.js"></script>
    <script src="js/jquery_ui.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="src/facebox.js"></script>

 
    <script type="text/javascript">
       jQuery(document).ready(function($) {
    $("a[id*=popup]").facebox({
      loadingImage : 'src/img/loading.gif',
      closeImage   : 'src/img/closelabel.png'
    })
  })
    </script>

<script type="text/javascript">


//GET Medicine Name And Expire Date

  $(document).ready(function(){

       $("#qty").focus(

            function(){

              var medicine_name = $("#product_hidden").val();
              var date_expiration   = $("#date_hidden").val();

            $.ajax({
              type:'POST',
              url :'auto.php',
              dataType:"json",
              data:{medicine_name:medicine_name,date_expiration:date_expiration},
              success:function(data){

                $("#avai_qty").val(data);
              },

            });
    });

//GET Medicine Name And Expire Date

         //Disabled Button If quantite Not Available

          $("#qty").blur(function(){

             var avai_qty = $("#avai_qty").val();
             var in_qty = parseInt($("#qty").val());
             var avai_qty_int = parseInt($("#avai_qty").val());
             if(avai_qty == "" ||  in_qty > avai_qty_int || in_qty <= 0){
                    
                    $("#btn_submit").attr('disabled','disabled');
                    alert("Something went wrong");

             }
             else{

              $("#btn_submit").removeAttr('disabled');

             }

          });

         //Disabled Button If quantite Not Available
});
     </script>

     <script language="javascript" type="text/javascript">

      //Clock

 var timerID = null;
var timerRunning = false;
function stopclock (){
if(timerRunning)
clearTimeout(timerID);
timerRunning = false;
}
function showtime () {
var now = new Date();
var hours = now.getHours();
var minutes = now.getMinutes();
var seconds = now.getSeconds()
var timeValue = "" + ((hours >12) ? hours -12 :hours)
if (timeValue == "0") timeValue = 12;
timeValue += ((minutes < 10) ? ":0" : ":") + minutes
timeValue += ((seconds < 10) ? ":0" : ":") + seconds
timeValue += (hours >= 12) ? " P.M." : " A.M."
document.clock.face.value = timeValue;
timerID = setTimeout("showtime()",1000);
timerRunning = true;
}
function startclock() {
stopclock();
showtime();
}
window.onload=startclock;

   //Clock
       
     </script>
    

</head>
<body>
 <div class="navbar navbar-inverse navbar-fixed-top"><!--*****Header******-->

      <div class=" navbar-inner" style="background: #000;">
        <div class="container-fluid">

          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
          </a>

          <a class="brand" href="#"><b>Pharmagest </b></a>
           <div class="nav-collapse">

            <ul class="nav pull-right">
               <li style="color: #ffffff ;padding: 10px;"><font color="green" size="5">ventes:</font>
                <strong><?php

                  include("dbcon.php");

                  $date = date("Y-m-d");

                  $select_sql = "SELECT sum(montant_total) from ventes where Date = '$date'";

                  $select_query = mysqli_query($con,$select_sql);

                  while($row = mysqli_fetch_array($select_query)){

                             echo $row['sum(montant_total)'];


                  }



                ?></strong>

              </li> 


              <li>
        <?php 
        // Assurez-vous que dbcon.php est inclus une seule fois en haut de votre script
        // avec include_once("dbcon.php"); et que $con est une connexion mysqli valide.

        $row2 = 0; // Initialisation à une valeur par défaut

        // Vérifiez si la connexion $con est établie et est un objet mysqli valide
        if (isset($con) && $con instanceof mysqli) {
            $quantite_seuil = "10"; // Le seuil pour la quantité
            
            // Pour une sécurité accrue, les requêtes préparées sont recommandées.
            // Voici une correction minimale conservant mysqli_query, avec échappement :
            $select_sql1 = "SELECT id FROM stock WHERE quantite_restante <= '" . mysqli_real_escape_string($con, $quantite_seuil) . "' AND status='Available'";
            $result1 = mysqli_query($con, $select_sql1);

            if ($result1) { 
                // Si la requête a réussi, $result1 est un objet mysqli_result
                $row2 = mysqli_num_rows($result1); // Utilisation correcte de mysqli_num_rows
                mysqli_free_result($result1); // Toujours libérer le jeu de résultats
            } else {
                // La requête a échoué. Vous pouvez enregistrer l'erreur ici pour le débogage.
                // error_log("Erreur de requête MySQLi pour l'alerte de quantité : " . mysqli_error($con));
                // $row2 reste à 0, ce qui sera géré par la condition ci-dessous.
            }
        } else {
            // La connexion à la base de données n'est pas disponible ou n'est pas un objet mysqli.
            // error_log("Connexion à la base de données non disponible ou invalide pour l'alerte de quantité.");
            // $row2 reste à 0.
        }

        if ($row2 == 0) {
            echo ' <a  href="#" class="notification label-inverse" >
                <span class="icon-exclamation-sign icon-large"></span></a>';
        } else {
            // Assurez-vous que htmlspecialchars est utilisé si $row2 peut contenir des caractères spéciaux,
            // bien que pour un nombre, ce soit moins critique ici.
            echo ' <a  href="qty_alert.php" class="notification label-inverse" id="popup">
                <span class="icon-exclamation-sign icon-large"></span>
                <span class="badge">' . $row2 . '</span></a>';
        }
        ?> 
        </li>
        <li>
            <?php
            // Assurez-vous que dbcon.php est inclus une seule fois en haut de votre script
            // avec include_once("dbcon.php"); et que $con est une connexion mysqli valide.

            $row1_count = 0; // Initialisation à une valeur par défaut

            // Vérifiez si la connexion $con est établie et est un objet mysqli valide
            if (isset($con) && $con instanceof mysqli) {
                $current_date_for_expiration_check = date("Y-m-d");
                // Pour PHP 5.3+, 'months' est plus correct que 'month' pour strtotime, mais 'month' fonctionne souvent.
                // Utilisons 'months' pour être plus précis.
                $expiration_threshold_date = date("Y-m-d", strtotime("+6 months", strtotime($current_date_for_expiration_check))); 
                
                // Échappement de la variable pour la sécurité (les requêtes préparées sont meilleures)
                $escaped_inc_date = mysqli_real_escape_string($con, $expiration_threshold_date);
                
                // Il est plus performant de sélectionner seulement ce dont vous avez besoin, ex: COUNT(id) ou juste un 'id'
                $select_sql_exp_alert = "SELECT id FROM stock WHERE date_expiration <= '$escaped_inc_date' AND status='Available'";
                $result_exp_alert = mysqli_query($con, $select_sql_exp_alert);

                if ($result_exp_alert) { 
                    // Si la requête a réussi, $result_exp_alert est un objet mysqli_result
                    $row1_count = mysqli_num_rows($result_exp_alert); // Utilisation correcte de mysqli_num_rows
                    mysqli_free_result($result_exp_alert); // Toujours libérer le jeu de résultats
                } else {
                    // La requête a échoué. Vous pouvez enregistrer l'erreur ici pour le débogage.
                    // error_log("Erreur de requête MySQLi pour l'alerte d'expiration : " . mysqli_error($con));
                    // $row1_count reste à 0, ce qui sera géré par la condition ci-dessous.
                }
            } else {
                // La connexion à la base de données n'est pas disponible ou n'est pas un objet mysqli.
                // error_log("Connexion à la base de données non disponible ou invalide pour l'alerte d'expiration.");
                // $row1_count reste à 0.
            }

            if ($row1_count == 0) {
                 echo ' <a  href="#" class="notification label-inverse" >
                <span class="icon-bell icon-large"></span></a>';
            } else {
                // Assurez-vous que htmlspecialchars est utilisé si $row1_count peut contenir des caractères spéciaux,
                // bien que pour un nombre, ce soit moins critique ici.
                echo ' <a  href="ex_alert.php" class="notification label-inverse" id="popup">
                <span class="icon-bell icon-large"></span>
                <span class="badge">' . $row1_count . '</span></a>';
            }
            ?>
          </li>
         <li><a href="product/view.php?numero_facture=<?php echo $_GET['numero_facture']?>"><span class="icon-bar-chart"></span>Produits</a></li>
          <li><a href="sales_report.php?numero_facture=<?php echo $_GET['numero_facture']?>"><span class="icon-bar-chart"></span>Rapport de vente</a></li>   
         <li><a href="logout.php" class="link"><font color='red'><span class="icon-off"></span></font>Deconnexion</a></li>
         <li><a href="backup.php?numero_facture=<?php echo $_GET['numero_facture']?>"><span class="icon-folder-open"></span>sauvegarde</a></li>
       </ul>
         </div>
        </div>
      </div>
  </div><!--*****Header******-->

 
 <div class="container">

    <i class="icon-calendar icon-large"></i>
    <?php
                $Today = date('Y-m-d');
                $new = date('l, F d, Y', strtotime($Today));
                echo $Today; 
                ?><br><br>
     

     <form name="clock" method="POST" action="#"><!--*****Clock******-->
     <input style="width:150px;background: #000;color: #fff;border-radius: 5px;height: 30px;" readonly type="submit" class="trans" name="face" value="">
      </form><!--*****Clock******-->
  </div>
   
   <div class="container">
     <div class="contentheader" style="width: 300px;">
      <h1>Vente ici</h1>
     </div><br><br>
   
  <div class="col-md-6">
     <form method="POST" action="insert_invoice.php?numero_facture=<?php echo $_GET['numero_facture']?> " >
      <input type="text" name="bar_code" id="barres_code" autocomplete="off" placeholder="Barcode scan ici" style="width:300px;height: 30px;"><br>
  <input type="text" id="product" required  autocomplete="off" placeholder="Entrer le Nom du Medicament" style="width:300px;height: 30px;"><br>
  <div class="ui-widget">
  <input type="hidden" name="product" id="product_hidden" required class="form-control" autocomplete="off" placeholder="Entrer le Nom du Medicament" style="width:300px;height: 30px;">
</div>
   <input type="hidden" name="date_expiration" id="date_hidden" required class="form-control" autocomplete="off" placeholder="Entrer le Nom du Medicament" style="width:300px;height: 30px;">
  <input type="number" name="avai_qty" id="avai_qty"  readonly placeholder="qte Disponible" style="width: 100px; height:30px;">

  <input type="number" name="qty" id="qty"  placeholder="Qte" autocomplete="off"  style="width: 100px; height:30px;" required>
  <input type="hidden" name="date" value="<?php echo date("m/d/Y");?>">
  <button type="submit" name="submit" class="btn btn-info" id="btn_submit" style="width: 123px; height:40px; margin-top:-8px;">Ajouter</button>

   </form> 
   </div>

  </div>

  
<div class="container" style="overflow-x:auto; overflow-y: auto;">
  <table class="table table-bordered" id="resultTable" data-responsive="table">

  <thead>
    <tr>
      <th> Nom Medicament </th>
      <th> Categorie</th>
      <th style="background: red;"> date_expiration</th>
      <th> Prix </th>
      <th> Qte </th>
      <th> Montant</th>
      <th> Action </th>
    </tr>
  </thead>

         <tbody>
          <?php
      $numero_facture= $_GET['numero_facture'];
      $medicine_name = "";
      $category= "";
      $quantite= "";

          include("dbcon.php");

          $select_sql = "SELECT * from en_attente where numero_facture = '$numero_facture' ";

          $select_query = mysqli_query($con ,$select_sql);

             $i = 0;
              
            while($row = mysqli_fetch_array($select_query)):

              $i++;
          ?>

          <tr class="record">
               <td><?php


                 $med_id = $row['id'];
                 $medicine_name=$row['medicine_name'];
                 echo $medicine_name;
                 echo "<input type='hidden' value=$med_id id='med_id$i' name='med_id'>";
                 echo "<input type='hidden' value=$medicine_name id='med_name$i' name='med_name'>"
                ?></td>
               <td><?php $category = $row['category'];
               echo $category;
                ?>
                   <input type="hidden" value='<?php echo $category?>' id='med_cat<?php echo $i?>' name='med_cat'>
                  
                </td>
                <td>
                  <?php 
                  $ex_date=$row['date_expiration'];
                  echo $ex_date;
                   ?>
                   <input type="hidden" id="ex_date<?php echo $i?>" value='<?php echo $ex_date?>'' name='ex_date'>

                </td>
               <td><?php echo  $row['cost']; ?></td>
               <td>
               <?php

                  $quantite =  $row['qty'];
                  $type     =  $row['type'];
                  echo "<input type='hidden' id='hid_quantite$i' value='$quantite' name='hid_quantite'>";
                  echo "<input type='number' id='quantite$i' name='quantite' value='$quantite' min='1' max='10' style='width:50px'>"."&nbsp;(".$type.")&nbsp;&nbsp;&nbsp;&nbsp;";
                  echo "<a href='#' class='qty_upd$i'><span class='icon-refresh'></span></a>";
                  echo "<div class='ajax-loader$i' style='visibility:hidden'>

                       <img src='src/img/loading.gif'>

                       </div>
                     ";
                               ?>
               </td>
               
               <td><?php echo $row['montant ']; ?></td>
     <td><a href="delete_invoice.php?numero_facture=<?php echo $_GET['numero_facture']?>&id=<?php echo $row['id'];?>&name=<?php echo $row['medicine_name']?>&date_expiration=<?php echo $row['date_expiration']?>&quantite=<?php echo $row['qty'];?>" class="btn btn-warning">Cancel</a></td>

            <?php endwhile; ?>  
          </tr>
          <tr>
        <th colspan="5" ><font size=6><strong> Total:</strong></font></th>
        <td  colspan="2"><strong>

          <?php

          $select_sql = "SELECT sum(montant ) , sum(montant_profit ) from en_attente where numero_facture = '$numero_facture'";

          $select_query= mysqli_query($con,$select_sql);

          while($row = mysqli_fetch_array($select_query)){

            $grand_total = $row['sum(montant )'];
            $grand_profit =$row['sum(montant_profit )'];
            echo $grand_total;
          }
          ?>
        </td>
      </tr>
  </tbody>
</table><br>

    <?php
     if($medicine_name && $category && $quantite !=null){
      ?>

      <a id="popup"  href="checkout.php?numero_facture=<?php echo $_GET['numero_facture']?>&medicine_name=<?php echo $medicine_name?>&category=<?php echo $category?>&ex_date=<?php echo $ex_date?>&quantite=<?php echo $quantite?>&total=<?php echo $grand_total?>&profit=<?php echo $grand_profit?>" style="width:400px;" class="btn btn-primary btn-large">Soumettre</a>

    <?php
     }else{


      ?>

      <h1>PAS de vente disponible</h1>

    <?php
 
          }

    ?>
    </div>
 
  </body>
 </html>
<script type="text/javascript">


  $(document).ready(function(){

     $("#product").focus(

            function(){

              var bar_code = $("#bar_code").val();

            $.ajax({
              type:'POST',
              url :'bar_code.php',
              dataType:"json",
              data:{bar_code:bar_code},
              success:function(data){

                $("#product").val(data);
              },

            });
    });

      //****AUTO COMPLETE*****
    $("#product").typeahead({

               source: function(drug_result, result){

            $.ajax({

          url : 'autocomplete.php',
          method :'POST',
          data :{drug_result:drug_result},
          dataType:"json",

          success:function(data){

            result($.map(data,function(item){



              return item;

            }));
          },

        });
      },

    });

      //****AUTO COMPLETE*****



     //****Medicine name and Date*****
     $("#product").focusout(function(){
         
               var value = $("#product").val();

               var res= value.split(",");

               var name = res[0];

               var date = res[1];

            $("#product_hidden").val(name);
          $("#date_hidden").val(date);

    });
    //****Medicine name and Date*****

    //*******Qty Update*******
  for(var i=1;i<=100;i++){

  $("a.qty_upd"+i).click(function(){

        for(var i1=1;i1<=100;i1++){

                var med_id=$("#med_id"+i1).val();
                var med_name=$("#med_name"+i1).val();
                var med_cat=$("#med_cat"+i1).val();
                var ex_date=$("#ex_date"+i1).val();
                var hid_qty = $("#hid_quantite"+i1).val();
                var qty=$("#quantite"+i1).val();

                if(qty <= 0){

                  alert("Sorry Error");

                }else{

             $.ajax({
              type:'POST',
               beforeSend:function(){
                 $('.ajax-loader'+i1).css("visibility", "visible");
              },
              url :'quantite_upd.php',
              data:{med_id:med_id,med_name:med_name,med_cat:med_cat,ex_date:ex_date,hid_qty:hid_qty,qty:qty},

              success:function(){

                location.reload();

              },

            });

           }

         }
  });

}
     //*******Qty Update*******

  });
</script>
 
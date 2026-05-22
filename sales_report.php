<?php

   session_start();

  if(!isset($_SESSION['user_session'])){
    
      header("location:index.php");

  }

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>PHARMAGEST</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="src/facebox.css">
  <link rel="stylesheet" type="text/css" href="css/tcal.css">
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/facebox.js"></script>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
    $("a[id*=popup]").facebox({
      loadingImage : 'src/img/loading.gif',
      closeImage   : 'src/img/closelabel.png'
    })
  }) 
    </script>
    <script type="text/javascript" src="js/tcal.js"></script>
    <script type="text/javascript">

      function Clickheretoprint()
{ 
  var disp_setting="toolbar=yes,location=no,directories=yes,menubar=yes,"; 
      disp_setting+="scrollbars=yes,width=700, height=400, left=100, top=25"; 
  var content_vlue = document.getElementById("content").innerHTML; 
  
  var docprint=window.open("","",disp_setting); 
   docprint.document.open(); 
   docprint.document.write('</head><body onLoad="self.print()" style="width: 700px; font-size:11px; font-family:arial; font-weight:normal;">');          
   docprint.document.write(content_vlue); 
   docprint.document.close(); 
   docprint.focus(); 
}

      
    </script>


     
</head>
<body>
  <body style="height: 100%">
  <div class="navbar navbar-inverse navbar-fixed-top"><!--*****Header******-->
      <div class="navbar-inner" style="background: #000;">
        <div class="container-fluid">

          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
          </a>

          <a class="brand" href="#"><b>Pharmagest</b></a>

          <div class="nav-collapse">
            <ul class="nav pull-right">
               
               <li>

               
               <?php 
                // --- Alerte de quantité (corrigée et sécurisée) ---
                $qty_alert_count = 0;
                if (isset($con) && $con instanceof mysqli) {
                    $qty_threshold = "5";
                    // Note: Assurez-vous que le nom de la colonne est 'quantite_restante' comme dans les autres fichiers
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
                    echo '<a href="qty_alert.php" class="notification label-inverse" id="popup"><span class="icon-exclamation-sign icon-large"></span><span class="badge">' . $qty_alert_count . '</span></a>';
                }
                ?> 
               </li>
          <li>
          <?php
                // --- Alerte d'expiration (corrigée et sécurisée) ---
                $exp_alert_count = 0;
                if (isset($con) && $con instanceof mysqli) {
                    $exp_threshold_date = date("Y-m-d", strtotime("+6 months"));
                    // Note: Assurez-vous que le nom de la colonne est 'date_expiration'
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
                    echo '<a href="ex_alert.php" class="notification label-inverse" id="popup"><span class="icon-bell icon-large"></span><span class="badge">' . $exp_alert_count . '</span></a>';
                }
                ?>
               </li>
            
          </li>
        

              
        <li><a href="accueil.php?numero_facture=<?php echo $_GET['numero_facture']?>"><span class="icon-home"></span>Accueil</a></li>

         <li><a href="product/view.php?numero_facture=<?php echo $_GET['numero_facture']?>"><span class="icon-bar-chart"></span>Produits</a></li>

         <li><a href="logout.php" class="link"><font color='red'><span class="icon-off"></span></font>Deconnexion</a></li>
       </ul>
      </div>
        </div>
      </div>
  </div><br><br><!--*****Header******-->

      <div class="container">


    <div class="contentheader">

      <h2>Rapport de ventes</h2>

    </div><br>


 <center> <form action="ventes_report.php?numero_facture=<?php echo $_GET['numero_facture']?>" method="POST">
<strong>De : <input type="date" style="width: 223px; padding:14px;" name="d1" class="tcal" autocomplete="off" value="" /> To: <input type="date" style="width: 223px; padding:14px;" name="d2" autocomplete="off" class="tcal" value="" />
 <button class="btn btn-info" style="width: 123px; height:50px; margin-top:-8px;margin-left:8px;" type="submit" name="submit"><i class="icon icon-search icon-large"></i> Recherches</button>
</strong>
</form></center>

            <div class="container" style="overflow-x:auto; overflow-y: auto;">


     <table class="table table-bordered">

          <tr>
            <th>Date</th>
            <th>numero_facture</th>
           <th>medicaments</th>
           <th>qte(Type)</th>
            <th>Total montant</th>
            <th>Total Profit</th>  
            <th>Action</th>
          <!--  <th>Action</th>-->
          </tr>

        <?php

            include("dbcon.php");
            error_reporting(1);
            if(isset($_POST['submit'])){
            $d1=$_POST['d1'];
            $d2=$_POST['d2'];
            $select_sql = "SELECT * FROM ventes where Date BETWEEN '$d1' and '$d2' order by Date desc";
            $select_query = mysqli_query($con,$select_sql);
            while($row = mysqli_fetch_array($select_query)) :
         ?>
          <tbody>
          <tr>
            <td><?php echo $row['Date']?></td>
            <td><?php $numero_facture =  $row['numero_facture'];

                 echo $numero_facture;

                 ?></td>
          
            <td><?php echo $row['medicaments']?></td>
            <td><?php echo $row['quantity']?></td>
            <td><?php echo $row['montant_total']?></td>
            <td><?php echo $row['total_profit']?></td>
                <td><a href="download.php?numero_facture=<?php echo $numero_facture?>"><button class="btn btn-info btn-large"><span class="icon-download"></span></button></a>
             </td>

                                     <?php endwhile;?>

          </tr>
          </tbody>

          <th colspan="4">Total:</th>
              <th>
                <?php

                $select_sql = "SELECT sum(montant_total) from ventes where Date BETWEEN '$d1' and '$d2'";

                $select_query = mysqli_query($con, $select_sql);

                while($row = mysqli_fetch_array($select_query)){

                   echo $row['sum(montant_total)'];

              }

                ?>
              </th>
              <th colspan="2">
                <?php

                $select_sql = "SELECT sum(total_profit) from ventes where Date BETWEEN '$d1' and '$d2'";

                $select_query = mysqli_query($con, $select_sql);

                while($row = mysqli_fetch_array($select_query)){

                   echo $row['sum(total_profit)'];
              }
                ?>
                          <?php }else{




                          $select_sql = "SELECT * FROM ventes where Date = '$date'";
                          $select_query = mysqli_query($con,$select_sql);
                          while($row = mysqli_fetch_array($select_query)) :


                            ?>

                             <tbody>
          <tr> 
            <td><?php echo $row['Date']?>&nbsp;&nbsp;(<font size='2' color='brown'>Today</font>)</td>
            <td><?php $numero_facture =  $row['numero_facture'];

                 echo $numero_facture;

                 ?></td>
          
           <td><?php echo $row['medicaments']?></td>
           <td><?php echo $row['quantity']?></td>

            <td><?php echo $row['montant_total']?></td>
            <td><?php echo $row['total_profit']?></td>
            <td><a href="download.php?numero_facture=<?php echo $numero_facture?>"><button class="btn btn-info btn-large"><span class="icon-download"></span></button></a>
        </td>
       <?php endwhile;?>

          </tr>
          </tbody>

           <th colspan="4">Total:</th>
              <th>
                <?php

                $select_sql = "SELECT sum(montant_total) from ventes where Date = '$date'";

                $select_query = mysqli_query($con, $select_sql);

                while($row = mysqli_fetch_array($select_query)){

                   echo $row['sum(montant_total)'];

              }

                ?>
              </th>
              <th colspan="2">
                <?php

                $select_sql = "SELECT sum(total_profit) from ventes where Date = '$date'";

                $select_query = mysqli_query($con, $select_sql);

                while($row = mysqli_fetch_array($select_query)){

                   echo $row['sum(total_profit)'];
              }
                ?>

                          <?php } ?>
              </th>

      </table>

   </div>
  </div>
  </body>
</html>



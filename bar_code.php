
<?php

   include("dbcon.php");

session_start();

if(!isset($_SESSION['user_session'])){

    header("location:index.php");
}
   @$bar_code=mysqli_real_escape_string($con,$_POST['barres_code']);

   $query="SELECT * from stock where barres_code = '$bar_code' and statut= 'Available'
   ";

   $result =mysqli_query($con,$query);

   $data= array();


   while($row = mysqli_fetch_array($result)){


   	$data [] = $row["nom_medicament"].",".$row['date_expiration'].",(".$row['type_vente'].")";

   }
     echo json_encode($data);

?>
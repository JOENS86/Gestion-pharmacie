
<?php

   include("dbcon.php");

session_start();

if(!isset($_SESSION['user_session'])){

    header("location:index.php");
}
   @$drug_result=mysqli_real_escape_string($con,$_POST['drug_result']);

   $query="SELECT * from stock where nom_medicament LIKE'%".$drug_result."%' and statut= 'Available'
   ";

   $result =mysqli_query($con,$query);

   $data= array();


   while($row = mysqli_fetch_array($result)){


   	$data [] = $row["nom_medicament"].",".$row['date_expiration'].",(".$row['type_vente'].")";

   }
     echo json_encode($data);

?>
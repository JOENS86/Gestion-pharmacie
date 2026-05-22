<?php

session_start();

// Vérification de session utilisateur
if(!isset($_SESSION['session_utilisateur'])){
    header("location:index.php");
}
?>

<?php

include("connexion_bd.php");

// Récupération des données via GET
$id_produit = $_GET['id'];
$nom_medicament = $_GET['nom_medicament'];
$date_expiration = $_GET['date_expiration'];
$quantite  = $_GET['quantite'];
$numero_facture = $_GET['numero_facture'];

// *** METTRE À JOUR LE STOCK lorsque le médicament est supprimé de la vente ***
$requete_mise_a_jour = "UPDATE stock 
                        SET quantite_utilisee = quantite_utilisee - '$quantite', 
                            quantite_restante = quantite_restante + '$quantite', 
                            statut = 'Disponible' 
                        WHERE nom_medicament = '$nom_medicament' 
                        AND date_expiration = '$date_expiration'";

$execution_mise_a_jour = mysqli_query($con, $requete_mise_a_jour);

// *** SUPPRIMER DE 'en_attente' lorsque le médicament est supprimé de la vente ***
$requete_suppression = "DELETE FROM `en_attente` WHERE id_produit = '$id_produit'";
$execution_suppression = mysqli_query($con, $requete_suppression);

// Redirection ou message d'erreur
if($execution_suppression){
    header("location:accueil.php?numero_facture=$numero_facture");
} else {
    echo "Désolé, une erreur est survenue lors de la suppression.";
}
?>

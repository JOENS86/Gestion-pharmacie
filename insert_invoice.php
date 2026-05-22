<?php

session_start();

if(!isset($_SESSION['session_utilisateur'])){
    header("location:index.php");
}

include("connexion_bd.php");

if(isset($_POST['soumettre'])){

    $numero_facture = $_GET['numero_facture'];
    $produit = $_POST['produit'];
    $date_expiration = $_POST['date_expiration'];
    $quantite = $_POST['quantite'];
    $date = $_POST['date'];

    $requete_select = "SELECT * FROM stock WHERE nom_medicament = '$produit' AND date_expiration = '$date_expiration'";
    $resultat_select = mysqli_query($con, $requete_select);

    while($ligne = mysqli_fetch_array($resultat_select)){
        $nom_medicament = $ligne['nom_medicament'];
        $categorie = $ligne['categorie'];
        $ancienne_quantite = $ligne['quantite'];
        $type_vente = $ligne['type_vente'];
        $prix_vente = $ligne['prix_vente'];
        $prix_profit = $ligne['prix_profit'];
        $date_expiration = $ligne['date_expiration'];
    }

    // Mettre à jour les quantités dans le stock après vente
    $requete_mise_a_jour = "UPDATE stock 
                            SET quantite_utilisee = quantite_utilisee + '$quantite', 
                                quantite_restante = quantite_restante - '$quantite' 
                            WHERE nom_medicament = '$produit' 
                            AND date_expiration = '$date_expiration'";
    mysqli_query($con, $requete_mise_a_jour);

    // Recalculer la quantité restante
    $resultat_verif = mysqli_query($con, $requete_select);
    while($ligne = mysqli_fetch_array($resultat_verif)){
        $quantite_restante = $ligne['quantite_restante'];
    }

    echo "<h1>....CHARGEMENT</h1>";

    // Si la quantité est à zéro, marquer comme "Indisponible"
    if($quantite_restante <= 0){
        $requete_statut = "UPDATE stock 
                           SET statut = 'Indisponible' 
                           WHERE nom_medicament = '$produit' 
                           AND date_expiration = '$date_expiration'";
        mysqli_query($con, $requete_statut);
    }

    // Calcul des montants
    $montant_total = $quantite * $prix_vente;
    $montant_profit = $quantite * $prix_profit;

    // Insertion dans la table des produits en attente
    $requete_insertion = "INSERT INTO en_attente 
        VALUES('', '$numero_facture', '$nom_medicament', '$categorie', '$date_expiration', '$quantite', '$type_vente', '$prix_vente', '$montant_total', '$montant_profit', '$date')";
    
    if(mysqli_query($con, $requete_insertion)){
        header("location:accueil.php?numero_facture=$numero_facture");
    }
}
?>

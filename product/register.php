<?php
/**
 * Ce script gère l'ajout d'un nouveau médicament dans le stock.
 * Il est sécurisé contre les injections SQL grâce aux requêtes préparées.
 */

// Inclure la connexion à la base de données une seule fois.
include_once("../dbcon.php");

// Démarrer la session.
session_start();

// Vérifier si l'utilisateur est connecté.
if (!isset($_SESSION['user_session'])) {
    header("location:index.php");
    exit(); // Toujours utiliser exit() après une redirection.
}

// Vérifier si le formulaire a été soumis.
if (isset($_POST['submit'])) {

    // --- Récupération et nettoyage des données du formulaire ---

    // Récupérer le numéro de facture depuis l'URL pour la redirection.
    $numero_facture = isset($_GET['numero_facture']) ? $_GET['numero_facture'] : '';

    // Récupérer les données du formulaire.
    $code_barres = $_POST['code_barres'];
    $nom_medicament = $_POST['nom_medecine']; // Le nom dans le formulaire est 'nom_medecine'
    $categorie = $_POST['categorie'];
    $quantite = (int)$_POST['quantite']; // S'assurer que c'est un entier.
    $compagnie = $_POST['compagnie'];
    $type_vente = $_POST['type_vente'];
    $prix_achat = (float)$_POST['prix_achat']; // S'assurer que c'est un nombre à virgule.
    $prix_vente = (float)$_POST['prix_vente'];
    $prix_benefice = (float)$_POST['prix_profit']; // Le nom dans le formulaire est 'prix_profit'

    // Traitement sécurisé des dates.
    $date_enregistrement_fournie = $_POST['date_enregistrement'];
    $date_enregistrement_finale = !empty($date_enregistrement_fournie) ? date('Y-m-d', strtotime($date_enregistrement_fournie)) : null;

    $date_expiration_fournie = $_POST['date_expiration'];
    $date_expiration_finale = !empty($date_expiration_fournie) ? date('Y-m-d', strtotime($date_expiration_fournie)) : null;

    // --- Logique métier ---
    $quantite_restante = $quantite;
    $quantite_utilisee = 0; // Un nouveau produit a une quantité utilisée de 0.
    $statut = "Available"; // Statut par défaut.

    // Vérifier que la connexion à la base de données est valide.
    if (!isset($con) || !($con instanceof mysqli)) {
        die("Erreur critique : la connexion à la base de données a échoué.");
    }

    // --- Insertion sécurisée avec une requête préparée ---

    // La requête SQL avec des placeholders (?) pour la sécurité.
    $sql = "INSERT INTO stock (
                code_barres, nom_medicament, categorie, quantite, quantite_restante, 
                quantite_utilisee, date_enregistrement, date_expiration, compagnie, type_vente, 
                prix_achat, prix_vente, prix_benefice, statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Préparer la requête.
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        // Lier les variables aux placeholders. 'sssiisssssddds' définit le type de chaque variable.
        // s = string, i = integer, d = double (float)
        mysqli_stmt_bind_param(
            $stmt, 
            "sssiisssssddds",
            $code_barres,
            $nom_medicament,
            $categorie,
            $quantite,
            $quantite_restante,
            $quantite_utilisee,
            $date_enregistrement_finale,
            $date_expiration_finale,
            $compagnie,
            $type_vente,
            $prix_achat,
            $prix_vente,
            $prix_benefice,
            $statut
        );

        // Exécuter la requête.
        if (mysqli_stmt_execute($stmt)) {
            // Si l'insertion réussit, rediriger l'utilisateur vers la page de visualisation.
            header("location:view.php?numero_facture=" . urlencode($numero_facture));
            exit();
        } else {
            // Gérer l'échec de l'exécution.
            echo "Erreur lors de l'ajout du médicament : " . mysqli_stmt_error($stmt);
        }

        // Fermer le statement.
        mysqli_stmt_close($stmt);
    } else {
        // Gérer l'échec de la préparation.
        echo "Erreur de préparation de la requête : " . mysqli_error($con);
    }

    // Fermer la connexion.
    mysqli_close($con);
}
?>
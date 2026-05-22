<?php
/**
 * Ce script met à jour la quantité d'un produit dans une vente en attente.
 * Il ajuste également les niveaux de stock en conséquence.
 * Toutes les opérations sont effectuées dans une transaction pour garantir l'intégrité des données.
 */

// Démarrer la session en premier
session_start();

// Inclure la connexion à la base de données une seule fois
include_once("dbcon.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_session'])) {
    // Si non connecté, renvoyer une erreur HTTP 403 (Interdit) et arrêter le script.
    http_response_code(403);
    echo json_encode(['erreur' => 'Accès non autorisé.']);
    exit();
}

// Vérifier si la connexion à la base de données est valide
if (!isset($con) || !($con instanceof mysqli)) {
    http_response_code(500); // Erreur interne du serveur
    echo json_encode(['erreur' => 'Erreur de connexion à la base de données.']);
    exit();
}

// --- Récupération et validation des données POST ---
// On s'assure que les quantités sont des nombres entiers.
$qte_cachee = isset($_POST['hid_qty']) ? (int)$_POST['hid_qty'] : 0;
$nouvelle_qte = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
$medicament_id = isset($_POST['med_id']) ? $_POST['med_id'] : null;
$nom_medicament = isset($_POST['med_name']) ? $_POST['med_name'] : null;
$categorie_medicament = isset($_POST['med_cat']) ? $_POST['med_cat'] : null;
$date_expiration = isset($_POST['ex_date']) ? $_POST['ex_date'] : null;

// Vérifier que toutes les données nécessaires sont présentes
if ($nouvelle_qte <= 0 || !$medicament_id || !$nom_medicament || !$categorie_medicament || !$date_expiration) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['erreur' => 'Données manquantes ou invalides. La quantité doit être supérieure à zéro.']);
    exit();
}

// --- Début de la transaction ---
// Cela garantit que toutes les requêtes sont exécutées avec succès, ou aucune ne l'est.
mysqli_begin_transaction($con);

try {
    // --- Étape 1: Récupérer les informations du stock (prix et quantité disponible) ---
    $sql_select_stock = "SELECT prix_vente, prix_benefice, quantite_restante_actuelle FROM stock WHERE nom_medicament = ? AND categorie = ? AND date_expiration = ?";
    $stmt_select_stock = mysqli_prepare($con, $sql_select_stock);
    mysqli_stmt_bind_param($stmt_select_stock, "sss", $nom_medicament, $categorie_medicament, $date_expiration);
    mysqli_stmt_execute($stmt_select_stock);
    $resultat_stock = mysqli_stmt_get_result($stmt_select_stock);
    $stock_info = mysqli_fetch_assoc($resultat_stock);
    mysqli_stmt_close($stmt_select_stock);

    if (!$stock_info) {
        throw new Exception("Produit non trouvé dans le stock.");
    }

    $quantite_disponible = $stock_info['quantite_restante_actuelle'];

    // --- Étape 2: Vérifier si la quantité demandée est disponible ---
    // La quantité disponible doit être suffisante pour la *différence* par rapport à ce qui était déjà en attente.
    if ($nouvelle_qte > ($quantite_disponible + $qte_cachee)) {
        throw new Exception("Quantité demandée non disponible en stock. Disponible : " . ($quantite_disponible + $qte_cachee));
    }

    // --- Étape 3: Mettre à jour la table 'stock' ---
    // Calcul de la différence de quantité pour la mise à jour
    $diff_qte = $nouvelle_qte - $qte_cachee;
    
    $sql_update_stock = "UPDATE stock SET quantite_utilisee = quantite_utilisee + ?, quantite_restante = quantite_restante - ? WHERE nom_medicament = ? AND categorie = ? AND date_expiration = ?";
    $stmt_update_stock = mysqli_prepare($con, $sql_update_stock);
    mysqli_stmt_bind_param($stmt_update_stock, "iisss", $diff_qte, $diff_qte, $nom_medicament, $categorie_medicament, $date_expiration);
    if (!mysqli_stmt_execute($stmt_update_stock)) {
        throw new Exception("Erreur lors de la mise à jour du stock.");
    }
    mysqli_stmt_close($stmt_update_stock);

    // --- Étape 4: Mettre à jour la table 'en_attente' ---
    $montant = $nouvelle_qte * $stock_info['prix_vente'];
    $montant_benefice = $nouvelle_qte * $stock_info['prix_benefice'];

    $sql_update_attente = "UPDATE en_attente SET qte = ?, montant = ?, montant_benefice = ? WHERE id = ?";
    $stmt_update_attente = mysqli_prepare($con, $sql_update_attente);
    mysqli_stmt_bind_param($stmt_update_attente, "iddi", $nouvelle_qte, $montant, $montant_benefice, $medicament_id);
    if (!mysqli_stmt_execute($stmt_update_attente)) {
        throw new Exception("Erreur lors de la mise à jour de la vente en attente.");
    }
    mysqli_stmt_close($stmt_update_attente);

    // --- Étape 5: Mettre à jour le statut du stock si nécessaire ---
    $quantite_restante_finale = $quantite_disponible - $diff_qte;
    $nouveau_statut = ($quantite_restante_finale > 0) ? 'Available' : 'Unavailable';

    $sql_update_statut = "UPDATE stock SET statut = ? WHERE nom_medicament = ? AND date_expiration = ?";
    $stmt_update_statut = mysqli_prepare($con, $sql_update_statut);
    mysqli_stmt_bind_param($stmt_update_statut, "sss", $nouveau_statut, $nom_medicament, $date_expiration);
    if (!mysqli_stmt_execute($stmt_update_statut)) {
        throw new Exception("Erreur lors de la mise à jour du statut du stock.");
    }
    mysqli_stmt_close($stmt_update_statut);

    // --- Si tout s'est bien passé, on valide la transaction ---
    mysqli_commit($con);
    // Envoyer une réponse de succès au client (par exemple, pour recharger la page en JavaScript)
    echo json_encode(['succes' => 'Quantité mise à jour avec succès.']);

} catch (Exception $e) {
    // --- En cas d'erreur, on annule toutes les modifications ---
    mysqli_rollback($con);
    http_response_code(500); // Erreur interne du serveur
    // Envoyer un message d'erreur clair au client
    echo json_encode(['erreur' => $e->getMessage()]);
}

?>
<?php
include "dbcon.php";
require "fpdf.php";

session_start();

if (!isset($_SESSION['user_session'])) {
    header("location:index.php");
    exit();
}

class FacturePDF extends FPDF {

    function header() {
        $numero_facture = $_POST['invoice_number'];
        $date = $_POST['date'];

        $this->SetFont('Arial', 'B', 20);
        $this->Cell(276, 10, 'Facture Pharmacie', 0, 0, 'C');
        $this->Ln(20);
        $this->Cell(80, 40, 'N° Facture : ' . $numero_facture, 0, 0, 'C');
        $this->Ln();
        $this->Cell(50, -10, 'Date : ' . $date, 0, 0, 'C');
        $this->Ln(10);
    }

    function footer() {
        $this->Cell(276, 10, 'Merci de votre visite', 0, 0, 'C');
        $this->Ln(20);
    }

    function enTeteTableau() {
        $this->SetFont('Times', 'B', 15);
        $this->Cell(40, 10, 'Nom Produit', 1, 0, 'C');
        $this->Cell(40, 10, 'Catégorie', 1, 0, 'C');
        $this->Cell(40, 10, 'Quantité', 1, 0, 'C');
        $this->Cell(50, 10, 'Prix', 1, 0, 'C');
        $this->Cell(100, 10, 'Montant', 1, 0, 'C');
        $this->Ln();
    }

    function afficherTableau() {
        include "dbcon.php";

        $montant_paye = $_POST['paid_amount'];
        $numero_facture = $_POST['invoice_number'];

        $sql = "SELECT * FROM en_attente WHERE numero_facture = '$numero_facture'";
        $resultat = mysqli_query($con, $sql);

        while ($ligne = mysqli_fetch_array($resultat)) {
            $this->SetFont('Times', '', 12);
            $this->Cell(40, 10, $ligne['nom_medicament'], 1, 0, 'C');
            $this->Cell(40, 10, $ligne['categorie'], 1, 0, 'C');
            $this->Cell(40, 10, $ligne['quantite'] . " (" . $ligne['type'] . ")", 1, 0, 'C');
            $this->Cell(50, 10, $ligne['cout'], 1, 0, 'C');
            $this->Cell(100, 10, $ligne['montant'], 1, 0, 'C');
            $this->Ln();
        }

        $sql = "SELECT SUM(montant) as total FROM en_attente WHERE numero_facture = '$numero_facture'";
        $res = mysqli_query($con, $sql);
        $total = mysqli_fetch_assoc($res)['total'];

        $this->Cell(170, 10, 'Montant total', 1, 0, 'C');
        $this->Cell(100, 10, $total, 1, 0, 'C');
        $this->Ln();

        $this->SetFont('Times', 'B', 15);
        $this->Cell(170, 10, 'Montant payé', 1, 0, 'C');
        $this->Cell(100, 10, $montant_paye, 1, 0, 'C');
        $this->Ln();

        $this->SetFont('Times', 'B', 20);
        $this->Cell(170, 10, 'Monnaie rendue', 1, 0, 'C');
        $this->Cell(100, 10, $montant_paye - $total, 1, 0, 'C');
        $this->Ln(20);
    }

    function genererNumeroFacture() {
        $caracteres = "09302909209300923";
        srand((double)microtime() * 1000000);
        $facture = '';
        for ($i = 1; $i <= 7; $i++) {
            $num = rand() % 10;
            $tmp = substr($caracteres, $num, 1);
            $facture .= $tmp;
        }
        return $facture;
    }
}

// --- Création des dossiers ---
$date_dossier = date("Ymd");

@mkdir("C:/factures");
@mkdir("C:/factures/$date_dossier");
@mkdir("C:/factures/toutes_les_factures");

if (isset($_POST['submit'])) {
    $numero_facture = $_POST['invoice_number'];
    $date = $_POST['date'];
    $noms_medicaments = $_POST['medicine_name'];
    $medicaments = implode(",", $noms_medicaments);
    $quantites = $_POST['qty'];
    $quantite_type = implode(",", $quantites);
    $fichier_pdf = "facture-" . $numero_facture . ".pdf";

    $pdf = new FacturePDF();
    $pdf->AddPage('L', 'A4', 0);
    $pdf->enTeteTableau();
    $pdf->afficherTableau();

    $pdf->Output("C:/factures/$date_dossier/$fichier_pdf", 'F');
    $pdf->Output("C:/factures/toutes_les_factures/$fichier_pdf", 'F');

    // Insertion dans ventes
    $query_totaux = mysqli_query($con,
        "SELECT numero_facture, SUM(montant) AS total, SUM(montant_profit) AS profit
         FROM en_attente WHERE numero_facture = '$numero_facture'"
    );
    $ligne = mysqli_fetch_array($query_totaux);
    $montant_total = $ligne['total'];
    $profit_total = $ligne['profit'];

    $insertion = mysqli_query($con,
        "INSERT INTO ventes VALUES('', '$numero_facture', '$medicaments', '$quantite_type', '$montant_total', '$profit_total', '$date')"
    );

    if ($insertion) {
        echo "Facture enregistrée avec succès.";
    } else {
        echo "Erreur lors de l'enregistrement de la facture.";
    }

    $nouveau_numero = "RS-" . $pdf->genererNumeroFacture();
    header("Location: accueil.php?invoice_number=$nouveau_numero");
    exit();
}
?>

<?php
require('libs/fpdf/fpdf.php'); // Ensure the path to FPDF is correct

if (isset($_POST['download_receipt'])) {
    // Sanitize and capture form input
    $name = htmlspecialchars($_POST['name'] ?? 'N/A');
    $program = strip_tags($_POST['program'] ?? 'N/A');

    $amount = floatval($_POST['amount'] ?? 0);

    $date = date("Y-m-d");

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Header
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Donation Receipt', 0, 1, 'C');
    $pdf->Ln(5);

    // Organization Info
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'GOOD HOPE HOME Orphanage', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Date: ' . $date, 0, 1, 'C');
    $pdf->Ln(10);

    // Donor Info
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Donor Information', 0, 1);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, 'Name: ' . $name, 0, 1);
    $pdf->Cell(0, 8, 'Program Supported: ' . $program, 0, 1);
    $pdf->Cell(0, 8, 'Donation Amount: TSh ' . number_format($amount, 2), 0, 1);

    $pdf->Ln(10);
    $pdf->MultiCell(0, 8, "Thank you for your generous support. Your donation helps us continue to care for and support the children at our orphanage. This receipt serves as confirmation of your donation.");

    $pdf->Ln(15);
    $pdf->Cell(0, 10, 'Signed:', 0, 1);
    $pdf->Cell(0, 10, '___________________________', 0, 1);
    $pdf->Cell(0, 10, 'Director, GOOD HOPE HOME', 0, 1);

    // Save the PDF on the server and send a link for download
    $fileName = 'Donation_Receipt_' . preg_replace("/[^a-zA-Z0-9]/", "_", $name) . '.pdf';
    $filePath = 'uploads/receipts/' . $fileName;

    // Make sure the directory exists
    if (!is_dir('uploads/receipts')) {
        mkdir('uploads/receipts', 0777, true);
    }

    // Output the PDF to a file
    $pdf->Output('F', $filePath);

    // Redirect the user to download the file
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    readfile($filePath);

    // Ensure no further output is sent after the PDF
    exit();
} else {
    echo "Invalid request.";
}
?>
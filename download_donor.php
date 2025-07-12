<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../db-connection.php';

// Get the donor ID from the URL
if (isset($_GET['id'])) {
    $donor_id = (int)$_GET['id'];

    // Prepare SQL to fetch the specific donation
    $stmt = $conn->prepare("SELECT * FROM donation WHERE d_id = ?");
    $stmt->bind_param("i", $donor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Set headers to download the file as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="donor_' . $row['d_id'] . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add the CSV column headers
        fputcsv($output, ['ID', 'Program', 'Amount', 'Donor Name', 'Email', 'Phone', 'Address', 'Status']);

        // Add the donation data
        fputcsv($output, [
            $row['d_id'],
            $row['program'],
            $row['amount'],
            $row['d_name'],
            $row['email'],
            $row['phone'],
            $row['d_address'],
            $row['status']
        ]);

        fclose($output);
    } else {
        echo "No donor found.";
    }
    $stmt->close();
} else {
    echo "Invalid donor ID.";
}

$conn->close();
exit;
?>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../db-connection.php';

// 1. Total confirmed general donations
$sql_total = "SELECT SUM(amount) AS total_amount 
              FROM donation 
              WHERE program = 'general donation' AND status = 'Confirmed'";

$result_total = $conn->query($sql_total);
$total_general = ($result_total && $result_total->num_rows > 0) 
    ? $result_total->fetch_assoc()['total_amount'] 
    : 0;

// 2. Total allocated from general donations
$sql_allocated = "SELECT SUM(allocated_amount) AS total_allocated 
                  FROM general_donation_allocation";

$result_allocated = $conn->query($sql_allocated);
$total_allocated = ($result_allocated && $result_allocated->num_rows > 0) 
    ? $result_allocated->fetch_assoc()['total_allocated'] 
    : 0;

// 3. Remaining balance
$remaining_general_donation = $total_general - $total_allocated;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>General Donation Balance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #f9f9f9;
            padding: 30px;
        }
        .ui.card {
            margin: auto;
            max-width: 500px;
        }
        .header {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="ui raised very padded text container segment">
        <h2 class="ui teal header">
            General Donation Balance
        </h2>
        <div class="ui segment">
            <h4 class="ui header">Total Confirmed Donations</h4>
            <p><strong>$<?= number_format($total_general, 2); ?></strong></p>
        </div>

        <div class="ui segment">
            <h4 class="ui header">Total Allocated Amount</h4>
            <p><strong>$<?= number_format($total_allocated, 2); ?></strong></p>
        </div>

        <div class="ui segment">
            <h3 class="ui header">Remaining Balance</h3>
            <p><strong>$<?= number_format($remaining_general_donation, 2); ?></strong></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.js"></script>
</body>
</html>

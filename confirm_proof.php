<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include_once __DIR__ . '/../db-connection.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "UPDATE payment_proofs SET is_confirmed = 1 WHERE id = $id";
    $conn->query($sql);
}

header("Location: uploaded_proofs.php");
exit;
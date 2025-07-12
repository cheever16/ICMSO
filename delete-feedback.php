<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../db-connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM feedback WHERE feed_id = $id");
}

header('Location: feedback.php'); // Adjust if your file is named differently
exit;
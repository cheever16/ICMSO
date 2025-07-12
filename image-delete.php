<?php
include '../db-connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['cid'])) {
    $cid = $_GET['cid'];

    // Fetch the photo to remove from the upload directory
    $result = mysqli_query($conn, "SELECT cphoto FROM children WHERE cid = $cid");
    $child = mysqli_fetch_assoc($result);
    $photo_path = "uploads/" . $child['cphoto'];

    // Delete the photo from the server if it exists
    if (!empty($child['cphoto']) && file_exists($photo_path)) {
        unlink($photo_path);
    }

    // Delete child record from database
    $sql = "DELETE FROM children WHERE cid = $cid";
    if (mysqli_query($conn, $sql)) {
        echo "Child record deleted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    header("Location: children.php");
    exit;
} else {
    echo "No child ID provided.";
}
?>
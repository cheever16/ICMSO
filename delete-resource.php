<?php
include '../db-connection.php';
// or wherever your $conn is defined

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM resources WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: programs.php"); // adjust to your current page
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
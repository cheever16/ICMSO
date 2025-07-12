<?php
include_once(__DIR__ . '/../../db-connection.php');

$pendingCount = 0;
if ($conn) {
    $result = $conn->query("SELECT COUNT(*) as pending FROM payment_proofs WHERE is_confirmed = 0");
    if ($result) {
        $row = $result->fetch_assoc();
        $pendingCount = $row['pending'];
    }
}
?>

<div class="ui inverted menu">
    <div class="header item">
        OFD Administrator
    </div>
    <a class="item" href="../index.php">Home</a>



    <div class="right menu">
        <a class="item" href="../logout.php">Logout</a>
    </div>
</div>
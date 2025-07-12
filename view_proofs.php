<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<?php
include '../components/header.php';
include_once __DIR__ . '/../db-connection.php';
?>

<style>
.proof-card {
    margin-bottom: 2em;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.proof-card img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
}

.proof-meta {
    margin-bottom: 1em;
}
</style>

<div class="ui container" style="padding-top: 2em;">
    <!-- Top Navigation Bar -->
    <?php include '../components/top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui stackable grid">
        <!-- Left menu -->
        <?php include '../components/side-menu.php'; ?>

        <!-- Right content -->
        <div class="twelve wide column">
            <div class="ui segment">
                <h2 class="ui header">📂 Uploaded Payment Proofs</h2>
                <div class="ui divider"></div>

                <?php
                if (!$conn) {
                    echo "<div class='ui negative message'>Database connection failed.</div>";
                } else {
                    $sql = "SELECT * FROM payment_proofs ORDER BY uploaded_at DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        echo "<div class='ui cards'>";
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = $row['is_confirmed'] ? "green" : "red";
                            $statusText = $row['is_confirmed'] ? "Confirmed" : "Pending";

                            echo "<div class='ui fluid card proof-card'>
                                    <div class='content'>
                                        <div class='header'>" . htmlspecialchars($row['payer_name']) . "</div>
                                        <div class='meta proof-meta'>" . htmlspecialchars($row['payer_email']) . "</div>
                                        <div class='description'>
                                            <p><strong>Uploaded:</strong> " . $row['uploaded_at'] . "</p>
                                            <p><strong>Status:</strong> <span class='ui $statusClass label'>$statusText</span></p>
                                            <img src='../uploads/proofs/" . htmlspecialchars($row['file_name']) . "' alt='Proof Image'>
                                        </div>
                                    </div>
                                    <div class='extra content'>
                                        <a href='../uploads/proofs/" . htmlspecialchars($row['file_name']) . "' target='_blank' class='ui blue button'>
                                            <i class='download icon'></i> Download Proof
                                        </a>
                                    </div>
                                </div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div class='ui message'>No payment proofs uploaded yet.</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?>
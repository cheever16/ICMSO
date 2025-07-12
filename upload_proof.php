<?php
include __DIR__ . '/db-connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] == 0) {
        // Get form input values securely
        $payer_name = mysqli_real_escape_string($conn, $_POST['payer_name']);
        $payer_email = mysqli_real_escape_string($conn, $_POST['payer_email']);

        // File details
        $file = $_FILES['proof_file'];
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Only allow PDF files
        if ($file_ext === 'pdf') {
            // Limit file size to 5MB
            if ($file_size <= 5 * 1024 * 1024) {
                $new_file_name = uniqid('proof_', true) . '.pdf';
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/uploads/proofs/';

                // Ensure the upload directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Move file
                if (move_uploaded_file($file_tmp_name, $upload_dir . $new_file_name)) {
                    $stmt = $conn->prepare("INSERT INTO payment_proofs (payer_name, payer_email, file_name, uploaded_at) VALUES (?, ?, ?, NOW())");
                    $stmt->bind_param("sss", $payer_name, $payer_email, $new_file_name);

                    if ($stmt->execute()) {
                        echo "<div class='ui positive message'>Proof of payment uploaded successfully.</div>";
                    } else {
                        echo "<div class='ui negative message'>Database error: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                } else {
                    echo "<div class='ui negative message'>Failed to upload file. Please try again.</div>";
                }
            } else {
                echo "<div class='ui negative message'>File size must be 5MB or less.</div>";
            }
        } else {
            echo "<div class='ui negative message'>Only PDF files are accepted. Please upload a valid PDF.</div>";
        }
    } else {
        echo "<div class='ui negative message'>No file uploaded or an error occurred.</div>";
    }
}
?>
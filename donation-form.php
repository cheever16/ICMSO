<?php
include './components/header.php';
include __DIR__ . '/db-connection.php';
?>

<div class="ui container">
    <?php include './components/top-menu.php'; ?>
    <div class="ui stackable grid">
        <?php include './components/side-menu.php'; ?>

        <div class="twelve wide column">
            <h2 class="ui header">
                <i class="donate icon"></i>
                <div class="content">Donation Application</div>
            </h2>

            <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_combined'])) {
    $program = mysqli_real_escape_string($conn, $_POST['program']);
    $amount = (int)$_POST['amount'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $transaction_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);

    $has_file = isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] === 0;
    $has_txn_id = !empty($transaction_id);

    if (!$has_file && !$has_txn_id) {
        echo "<div class='ui negative message'>Please upload a PDF or enter a Transaction ID.</div>";
    } else {
        // Insert donation
        $sql_donation = "INSERT INTO donation (program, amount, d_name, email, phone, d_address, status) 
                         VALUES ('$program', '$amount', '$name', '$email', '$phone', '$address', 'pending')";

        if ($conn->query($sql_donation) === TRUE) {
            // Handle proof file upload
            $proof_saved = false;
            $new_file_name = null;

            if ($has_file) {
                $file = $_FILES['proof_file'];
                $file_name = $file['name'];
                $file_tmp_name = $file['tmp_name'];
                $file_size = $file['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_ext = ['pdf'];

                if (in_array($file_ext, $allowed_ext) && $file_size <= 5 * 1024 * 1024) {
                    $new_file_name = uniqid('proof_', true) . '.' . $file_ext;
                    $upload_dir = 'uploads/proofs/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $upload_path = $upload_dir . $new_file_name;
                    $proof_saved = move_uploaded_file($file_tmp_name, $upload_path);
                }
            }

            // Prepare values for insertion into payment_proofs
            $file_val = $proof_saved ? "'$new_file_name'" : "NULL";
            $txn_val = $has_txn_id ? "'$transaction_id'" : "NULL";

            $sql_proof = "INSERT INTO payment_proofs (payer_name, payer_email, file_name, transaction_id, uploaded_at)
                          VALUES ('$name', '$email', $file_val, $txn_val, NOW())";

            if (!$conn->query($sql_proof)) {
                echo "<div class='ui negative message'>Error saving proof: " . $conn->error . "</div>";
            } else {
                echo "<div class='ui positive message'>Your donation is pending confirmation. Thank you!</div>";

                echo "<form action='generate_receipt.php' method='post' target='_blank'>
                        <input type='hidden' name='name' value='" . htmlspecialchars($name) . "' />
                        <input type='hidden' name='program' value='" . htmlspecialchars($program) . "' />
                        <input type='hidden' name='amount' value='" . htmlspecialchars($amount) . "' />
                        <input type='hidden' name='address' value='" . htmlspecialchars($address) . "' />
                        <button type='submit' name='download_receipt' class='ui blue button mt-2'>Download Receipt</button>
                      </form>";
            }
        } else {
            echo "<div class='ui negative message'>Error saving donation: " . $conn->error . "</div>";
        }
    }
}
?>


            <!-- Donation Form -->
            <form method="post" enctype="multipart/form-data" class="ui form segment">
                <h4 class="ui dividing header">Donation Details</h4>
                <div class="grouped fields">
                    <label>Choose a Program:</label>
                    <div class="field"><div class="ui radio checkbox"><input type="radio" name="program" value="Food & Nutrition" required><label>Food & Nutrition</label></div></div>
                    <div class="field"><div class="ui radio checkbox"><input type="radio" name="program" value="Education"><label>Education</label></div></div>
                    <div class="field"><div class="ui radio checkbox"><input type="radio" name="program" value="Health Care"><label>Health Care</label></div></div>
                    <div class="field"><div class="ui radio checkbox"><input type="radio" name="program" value="General Donation"><label>General Donation</label></div></div>
                </div>

                <div class="field">
                    <label>Amount (TSh)</label>
                    <input type="number" name="amount" min="1" placeholder="Enter amount in TSh" required>
                </div>

                <h4 class="ui dividing header">Donor Details</h4>
                <div class="field">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Your full name" required>
                </div>
                <div class="two fields">
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="you@example.com" required>
                    </div>
                    <div class="field">
                        <label>Phone</label>
                        <input type="tel" name="phone" placeholder="+255..." required>
                    </div>
                </div>
                <div class="field">
                    <label>Address</label>
                    <input type="text" name="address" placeholder="e.g. Arusha, Tanzania" required>
                </div>

                <h4 class="ui dividing header">Proof of Payment</h4>
                <div class="field">
                    <label>Upload PDF Receipt (optional)</label>
                    <input type="file" name="proof_file" accept=".pdf">
                </div>
                <div class="field">
                    <label>OR Enter Transaction ID (if no PDF)</label>
                    <input type="text" name="transaction_id" placeholder="Transaction Reference Code">
                </div>

                <button name="submit_combined" class="ui primary button" type="submit">
                    <i class="paper plane icon"></i> Submit Donation
                </button>
                <button class="ui red button" type="reset">
                    <i class="undo icon"></i> Reset
                </button>
            </form>

        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>

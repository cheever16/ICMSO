<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /ICMSO/login.php');
    exit;
}
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/admin/admin_components/admin_header.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/db-connection.php'; ?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/PHPMailer-master/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/PHPMailer-master/src/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/PHPMailer-master/src/Exception.php';

// Confirm Logic with Email
if (isset($_GET['confirm'])) {
    $donation_id = (int)$_GET['confirm'];

    // Get donor details
    $stmt = $conn->prepare("SELECT d_name, email, amount, program FROM donation WHERE d_id = ?");
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $donor = $result->fetch_assoc();
        $d_name = $donor['d_name'];
        $email = $donor['email'];
        $amount = $donor['amount'];
        $program = $donor['program'];

        // Update donation status
        $update = $conn->prepare("UPDATE donation SET status = 'confirmed' WHERE d_id = ?");
        $update->bind_param("i", $donation_id);

        if ($update->execute()) {
            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ellydecheever16@gmail.com';
                $mail->Password = 'atyg fkxg lyfn efza';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('ellydecheever16@gmail.com', 'GOOD HOPE HOME');
                $mail->addAddress($email, $d_name);

                $mail->isHTML(true);
                $mail->Subject = 'Donation Confirmation';
                $mail->Body    = "
                    <h3>Dear $d_name,</h3>
                    <p>We are delighted to confirm the receipt of your donation of <strong>$amount TSh</strong> towards our <strong>$program</strong> program.</p>
                    <p>Thank you for your generous support and contribution.</p>
                    <p>Best regards,<br>GOOD HOPE HOME Team</p>
                ";

                $mail->send();
                echo "<div class='ui green message'>Donation confirmed and email sent to $email.</div>";
            } catch (Exception $e) {
                echo "<div class='ui yellow message'>Donation confirmed, but email not sent. Mailer Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            echo "<div class='ui red message'>Error confirming donation: " . $update->error . "</div>";
        }

        $update->close();
    }

    $stmt->close();
}

// Delete Logic
if (isset($_GET['del'])) {
    $donor_id = (int)$_GET['del'];
    $stmt = $conn->prepare("DELETE FROM donation WHERE d_id = ?");
    $stmt->bind_param("i", $donor_id);
    if ($stmt->execute()) {
        echo "<div class='ui green message'>Donor deleted successfully.</div>";
    } else {
        echo "<div class='ui red message'>Error deleting donor: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<div class="ui container">
    <!-- Top Navigation Bar -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/admin/admin_components/admin_top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui grid" style="display: flex; flex-wrap: wrap; gap: 0; justify-content: flex-start; margin-top: 0;">

        <!-- Content here -->
    </div>

    <!-- Left menu (Side Menu) -->
    <div style="flex: 0 0 250px; padding-right: 20px;">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/admin/admin_components/admin_side-menu.php'; ?>
    </div>

    <table class="ui celled table">
        <thead>
            <tr>
                <th>#</th>
                <th>Program</th>
                <th>Amount</th>
                <th>Donor Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM donation ORDER BY d_id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status = strtolower(trim($row['status']));
                    $status_label = ($status === 'confirmed')
                        ? "<span class='ui green label'>Confirmed</span>"
                        : "<span class='ui red label'>Pending</span>";

                    $confirm_button = ($status !== 'confirmed')
                        ? "<a href='donators.php?confirm={$row['d_id']}' class='ui basic green button'>Confirm</a>"
                        : "<button class='ui basic green button' disabled>Confirmed</button>";

                    // Initialize proof info
                    $file_url = '';
                    $transaction_id = '';

                    // Get most recent payment proof for this donor email
                    $proof_stmt = $conn->prepare("SELECT file_name, transaction_id FROM payment_proofs WHERE payer_email = ? ORDER BY uploaded_at DESC LIMIT 1");
                    $proof_stmt->bind_param("s", $row['email']);
                    $proof_stmt->execute();
                    $proof_result = $proof_stmt->get_result();

                    if ($proof_result && $proof_result->num_rows > 0) {
                        $proof_row = $proof_result->fetch_assoc();
                        $ext = strtolower(pathinfo($proof_row['file_name'], PATHINFO_EXTENSION));
                        if ($ext === 'pdf' && !empty($proof_row['file_name'])) {
                            $file_url = '/ICMSO/uploads/proofs/' . $proof_row['file_name'];
                        } elseif (!empty($proof_row['transaction_id'])) {
                            $transaction_id = htmlspecialchars($proof_row['transaction_id']);
                        }
                    }
                    $proof_stmt->close();
            ?>
            <tr>
                <td><?= htmlspecialchars($row['d_id']) ?></td>
                <td><?= htmlspecialchars($row['program']) ?></td>
                <td><?= number_format($row['amount']) ?> TSh</td>
                <td><?= htmlspecialchars($row['d_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['d_address']) ?></td>
                <td><?= $status_label ?></td>
                <td>
                    <?= $confirm_button ?>
                    <a class="ui red button" href="donators.php?del=<?= $row['d_id'] ?>" onclick="return confirm('Are you sure you want to delete this donor?');">Delete</a>

                    <?php if (!empty($file_url)): ?>
                        <a href="<?= $file_url ?>" class="ui blue button" download="<?= basename($file_url) ?>" target="_blank">Download PDF</a>
                    <?php elseif (!empty($transaction_id)): ?>
                        <div class="ui yellow message" style="margin-top: 5px;">
                            Transaction ID: <strong><?= $transaction_id ?></strong>
                        </div>
                    <?php else: ?>
                        <div class="ui message" style="margin-top: 5px; font-style: italic;">
                            No proof or transaction ID uploaded
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='9' class='center aligned'>No donations found.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>

</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/admin/admin_components/admin_footer.php'; ?>

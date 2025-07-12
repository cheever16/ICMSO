<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/admin/admin_components/admin_header.php';
include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/db-connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/PHPMailer-master/src/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/PHPMailer-master/src/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/PHPMailer-master/src/Exception.php';
?>
<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './admin_components/admin_top-menu.php'; ?>

    <div class="ui container" style="display: flex; flex-wrap: wrap;">
        <!-- Sidebar (using flex layout) -->
        <div id="sidebar"
            style="flex: 0 0 220px; position: fixed; height: 100vh; padding-top: 2em; background-color: #ffffff; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1); z-index: 1000;">
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/admin/admin_components/admin_side-menu.php'; ?>
        </div>

        <!-- Main content area (using flex layout) -->
        <div class="ui grid" style="flex: 1 0 80%; margin-left: 220px; padding-top: 2em;">
            <div class="twelve wide column">
                <h2 class="ui header">Sponsor Management</h2>

                <?php
            // Confirm Logic
            if (isset($_GET['confirm']) && isset($_GET['cid'])) {
                $spn_id = (int)$_GET['confirm'];
                $cid = (int)$_GET['cid'];

                $stmt1 = $conn->prepare("UPDATE sponsorer SET confirmed = 1 WHERE spn_id = ?");
                $stmt1->bind_param("i", $spn_id);

                $stmt2 = $conn->prepare("UPDATE children SET sponsored = 1 WHERE cid = ?");
                $stmt2->bind_param("i", $cid);

                if ($stmt1->execute() && $stmt2->execute()) {
                    // Fetch sponsor details for email
                    $stmt = $conn->prepare("SELECT spn_firstname, spn_email FROM sponsorer WHERE spn_id = ?");
                    $stmt->bind_param("i", $spn_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $sponsor = $result->fetch_assoc();

                    $sponsor_name = $sponsor['spn_firstname'];
                    $sponsor_email = $sponsor['spn_email'];

                    // Send confirmation email using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'ellydecheever16@gmail.com';
                        $mail->Password = 'atyg fkxg lyfn efza'; // Be sure to handle sensitive data securely
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        $mail->setFrom('ellydecheever16@gmail.com', 'GOOD HOPE HOME');
                        $mail->addAddress($sponsor_email, $sponsor_name);

                        $mail->isHTML(true);
                        $mail->Subject = 'Sponsorship Confirmation';
                        $mail->Body    = "
                            <h3>Dear $sponsor_name,</h3>
                            <p>We are pleased to confirm your sponsorship for the child with ID $cid.</p>
                            <p>Your generous support is making a difference in the life of this child.</p>
                            <p>Thank you for your contribution to our cause!</p>
                            <p>Best regards,<br>GOOD HOPE HOME Team</p>
                        ";

                        $mail->send();
                        echo "<div class='ui green message'>Sponsor confirmed and email sent to $sponsor_email.</div>";
                    } catch (Exception $e) {
                        echo "<div class='ui yellow message'>Sponsor confirmed, but email not sent. Mailer Error: {$mail->ErrorInfo}</div>";
                    }
                } else {
                    echo "<div class='ui red message'>Error confirming sponsor.</div>";
                }

                $stmt1->close();
                $stmt2->close();
            }

            // Delete Logic
            if (isset($_GET['del']) && isset($_GET['cid'])) {
                $spn_id = (int)$_GET['del'];
                $cid = (int)$_GET['cid'];

                $stmt1 = $conn->prepare("DELETE FROM sponsorer WHERE spn_id = ?");
                $stmt1->bind_param("i", $spn_id);

                $stmt2 = $conn->prepare("UPDATE children SET sponsored = 0 WHERE cid = ?");
                $stmt2->bind_param("i", $cid);

                if ($stmt1->execute() && $stmt2->execute()) {
                    echo "<div class='ui green message'>Sponsor deleted successfully.</div>";
                } else {
                    echo "<div class='ui red message'>Error deleting sponsor.</div>";
                }

                $stmt1->close();
                $stmt2->close();
            }
            ?>

                <table class="ui celled table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Sponsored On</th>
                            <th>Years</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Check No.</th>
                            <th>Child ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    $sql = "SELECT * FROM sponsorer ORDER BY spn_id DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $formattedDate = date("d-m-Y H:i:s", strtotime($row['spnd_date']));
                            $spn_id = (int)$row['spn_id'];
                            $cid = (int)$row['cid'];
                            $email = $row['spn_email'];
                            $confirmed = $row['confirmed'];

                            $status_label = ($confirmed)
                                ? "<span class='ui green label'>Confirmed</span>"
                                : "<span class='ui red label'>Pending</span>";

                            $confirm_button = (!$confirmed)
                                ? "<a class='ui basic green button' href='?confirm={$spn_id}&cid={$cid}'>Confirm</a>"
                                : "<button class='ui basic green button' disabled>Confirmed</button>";

                            // Proof Logic: Fetch the sponsor's proof file from sponsor_proofs table
                            $file_url = '';
                            $proof_stmt = $conn->prepare("SELECT proof_file FROM sponsor_proofs WHERE sponsor_email = ? ORDER BY uploaded_at DESC LIMIT 1");
                            $proof_stmt->bind_param("s", $email);
                            $proof_stmt->execute();
                            $proof_result = $proof_stmt->get_result();

                            if ($proof_result && $proof_result->num_rows > 0) {
                                $proof_row = $proof_result->fetch_assoc();
                                $file_name = trim($proof_row['proof_file']);
                                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                                  
                                if ($ext === 'pdf') {
                                    $relative_path = '/ICMSO/admin/uploads/' . $file_name;
                                    $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $relative_path;
                                  
                                    if (file_exists($absolute_path)) {
                                        $file_url = $relative_path;
                                    } else {
                                        echo "<div class='ui red message'>File not found: {$file_name}</div>";
                                    }
                                }
                            }
                            $proof_stmt->close();
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['spn_firstname']) ?></td>
                            <td><?= $formattedDate ?></td>
                            <td><?= (int)$row['spn_noofyears'] ?></td>
                            <td><?= htmlspecialchars($email) ?></td>
                            <td><?= number_format((int)$row['spn_amount']) ?> TSh</td>
                            <td><?= htmlspecialchars($row['spn_checkno']) ?></td>
                            <td><?= $cid ?></td>
                            <td>
                                <?= $confirm_button ?>
                                <a class="ui red button" href="?del=<?= $spn_id ?>&cid=<?= $cid ?>"
                                    onclick="return confirm('Are you sure you want to delete this sponsor?');">
                                    Delete
                                </a>
                                <?php if (!empty($file_url)): ?>
                                <a href="<?= $file_url ?>" class="ui blue button" download="<?= basename($file_url) ?>"
                                    target="_blank">Download PDF</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                        }
                    } else {
                        echo "<tr><td colspan='8' class='center aligned'>No sponsors found.</td></tr>";
                    }
                    $conn->close();
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ICMSO/admin/admin_components/admin_footer.php'; ?>
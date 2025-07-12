<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<?php include './admin_components/admin_header.php'; ?>
<?php include '../db-connection.php'; ?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';
?>

<div class="ui container">
    <!-- Top Navigation Bar -->
    <?php include './admin_components/admin_top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui flex grid">
        <!-- Left menu -->
        <div class="four wide column">
            <?php include './admin_components/admin_side-menu.php'; ?>
        </div>

        <!-- Right content -->
        <div class="twelve wide column">
            <h1>Child Registration Requests</h1>

            <?php
            // Handle confirmation
            if (isset($_GET['confirm_id'])) {
                $id = intval($_GET['confirm_id']);
                $fetch = $conn->query("SELECT * FROM child_registration_requests WHERE id = $id");

                if ($fetch->num_rows > 0) {
                    $data = $fetch->fetch_assoc();

                    // Insert into children table including chealth_description if available
                    $cname = $conn->real_escape_string($data['cname']);
                    $cdob = $conn->real_escape_string($data['cdob']);
                    $cyoe = $conn->real_escape_string($data['cyoe']);
                    $cclass = $conn->real_escape_string($data['cclass']);
                    $chealth_status = $conn->real_escape_string($data['chealth_status']);
                    $cstory = $conn->real_escape_string($data['cstory']);
                    $chealth_description = isset($data['chealth_description']) ? $conn->real_escape_string($data['chealth_description']) : '';
                    $user_email = $data['email']; // Assumes email column exists

                    $insert = "INSERT INTO children (cname, cdob, cyoe, cclass, chealth_status, chealth_description, cstory) 
                               VALUES ('$cname', '$cdob', '$cyoe', '$cclass', '$chealth_status', '$chealth_description', '$cstory')";

                    if ($conn->query($insert) === TRUE) {
                        $conn->query("DELETE FROM child_registration_requests WHERE id = $id");

                        // Send email using PHPMailer
                        $mail = new PHPMailer(true);

                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'ellydecheever16@gmail.com'; // Replace with your Gmail address
                            $mail->Password = 'atyg fkxg lyfn efza';     // Replace with your app password
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;

                            $mail->setFrom('ellydecheever16@gmail.com', 'GOOD HOPE HOME');
                            $mail->addAddress($user_email);

                            $mail->isHTML(true);
                            $mail->Subject = 'Child Registration Confirmed';
                            $mail->Body    = "
                                <h3>Dear Parent/Guardian,</h3>
                                <p>Your child <strong>$cname</strong> has been successfully registered with GOOD HOPE HOME.</p>
                                <p>We appreciate your trust and look forward to a better future together.</p>
                                <p>Regards,<br>GOOD HOPE HOME Team</p>
                            ";

                            $mail->send();
                            echo "<div class='ui positive message'>Child confirmed, registered, and email sent successfully.</div>";
                        } catch (Exception $e) {
                            echo "<div class='ui warning message'>Child registered, but email not sent. Mailer Error: {$mail->ErrorInfo}</div>";
                        }
                    } else {
                        echo "<div class='ui negative message'>Error registering child: " . $conn->error . "</div>";
                    }
                }
            }

            // Display requests
            $sql = "SELECT * FROM child_registration_requests";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table class='ui celled table'>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>DOB</th>
                                <th>Enroll Year</th>
                                <th>Class</th>
                                <th>Health Status</th>
                                <th>Health Description</th>
                                <th>Story</th>
                                <th>Birth Cert (PDF)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['cname']}</td>
                            <td>{$row['cdob']}</td>
                            <td>{$row['cyoe']}</td>
                            <td>{$row['cclass']}</td>
                            <td>{$row['chealth_status']}</td>
                            <td>" . (!empty($row['chealth_description']) ? $row['chealth_description'] : '-') . "</td>
                            <td>{$row['cstory']}</td>
                            <td>" . (!empty($row['cpdf']) ? "<a href='../uploads/{$row['cpdf']}' target='_blank'>Download PDF</a>" : "No PDF") . "</td>
                            <td>
                                <a href='child-requests.php?confirm_id={$row['id']}' class='ui green button' onclick=\"return confirm('Are you sure you want to confirm this child?');\">Confirm</a>
                            </td>
                          </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No registration requests found.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</div>


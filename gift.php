<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include './admin_components/admin_header.php'; 
include '../db-connection.php';

// Check if a gift record needs to be deleted
if (isset($_GET['gift_id'])) {
    $gift_id = $_GET['gift_id'];

    // Prepare SQL statement to delete the gift record
    $sql = "DELETE FROM gift WHERE gift_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind the gift_id as an integer parameter
        $stmt->bind_param("i", $gift_id);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to gift-sent.php after successful deletion
            header('Location: gift-sent.php?message=Gift record deleted successfully');
            exit;
        } else {
            echo "<script>alert('Error in deleting the gift record. Please try again.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error in preparing the delete statement.');</script>";
    }
}

$conn->close();
?>

<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './admin_components/admin_top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui grid">
        <!-- Left menu -->
        <?php include './admin_components/admin_side-menu.php'; ?>

        <!-- right content -->
        <div class="twelve wide column">
            <h1>Gift Sent</h1>

            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Gift ID</th>
                        <th>Child ID</th>
                        <th>Type</th>
                        <th>Sending Date</th>
                        <th>Sender Name</th>
                        <th>Sender Email</th>
                        <th>Sender Phone</th>
                        <th>Sender Address</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM gift";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $unformated = $row['sending_date'];
                                $formateddate = date("d-m-Y", strtotime($unformated));
                    ?>
                    <tr>
                        <td><?php echo $row['gift_id']; ?></td>
                        <td><?php echo $row['cid']; ?></td>
                        <td><?php echo $row['gift_type']; ?></td>
                        <td><?php echo $formateddate; ?></td>
                        <td><?php echo $row['sender_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['sender_address']; ?></td>
                        <td>
                            <a class="ui red button"
                                onclick="return confirm('Are you sure you want to delete this gift record?');"
                                href="gift.php?gift_id=<?php echo $row['gift_id']; ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php 
                            }
                        }
                    ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<?php include './admin_components/admin_footer.php'; ?>
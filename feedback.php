<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../db-connection.php'; // Make sure this is included to define $conn
?>

<?php include './admin_components/admin_header.php' ?>

<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './admin_components/admin_top-menu.php' ?>

    <!-- BODY Content -->
    <div class="ui flex grid">
        <!-- Left menu -->
        <div class="four wide column">
            <?php include './admin_components/admin_side-menu.php' ?>
        </div>

        <!-- right content -->
        <div class="twelve wide column">
            <h1>Feed Back</h1>

            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Feed ID</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Comment</th>
                        <th>Action</th> <!-- NEW COLUMN -->
                    </tr>
                </thead>
                <tbody>

                    <?php
                        $sql = "SELECT * FROM feedback";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['feed_id']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['full_address']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['comment']; ?></td>
                        <td>
                            <a class="ui red mini button" href="delete-feedback.php?id=<?php echo $row['feed_id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this feedback?');">
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

<?php include './admin_components/admin_footer.php' ?>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<?php include './components/header.php'; ?>

<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './components/top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui grid">
        <!-- Left menu -->
        <?php include './components/side-menu.php'; ?>

        <!-- right content -->
        <div class="twelve wide column">
            <h1>Delete a Child Record</h1>

            <?php
                include 'db-connection.php';

                // Delete child if delete button clicked
                if (isset($_GET['delete_id'])) {
                    $child_id = intval($_GET['delete_id']);

                    // Get the image filename to delete it from uploads folder
                    $imgQuery = $conn->query("SELECT cphoto FROM children WHERE cid = $child_id");
                    if ($imgQuery && $imgQuery->num_rows > 0) {
                        $imgRow = $imgQuery->fetch_assoc();
                        $imagePath = "uploads/" . $imgRow['cphoto'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }

                    $delete = $conn->query("DELETE FROM children WHERE cid = $child_id");

                    if ($delete) {
                        echo "<div class='ui positive message'>Child record deleted successfully.</div>";
                    } else {
                        echo "<div class='ui negative message'>Failed to delete record.</div>";
                    }
                }

                // Fetch all children
                $children = $conn->query("SELECT * FROM children ORDER BY cid DESC");
            ?>

            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>DOB</th>
                        <th>Year of Enroll</th>
                        <th>Class</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($children && $children->num_rows > 0): ?>
                    <?php while ($row = $children->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['cphoto'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['cphoto']) ?>" width="50">
                            <?php else: ?>
                            N/A
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['cname']) ?></td>
                        <td><?= htmlspecialchars($row['cdob']) ?></td>
                        <td><?= htmlspecialchars($row['cyoe']) ?></td>
                        <td><?= htmlspecialchars($row['cclass']) ?></td>
                        <td>
                            <a href="?delete_id=<?= $row['cid'] ?>" class="ui red button"
                                onclick="return confirm('Are you sure you want to delete this child?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6">No children found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include './components/footer.php'; ?>
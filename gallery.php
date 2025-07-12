<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include './admin_components/admin_header.php';
include '../db-connection.php';

// DELETE IMAGE
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $getImage = $conn->query("SELECT image FROM gallery WHERE id=$id");
    if ($getImage && $getImage->num_rows > 0) {
        $imgRow = $getImage->fetch_assoc();
        $imagePath = "../uploads/" . $imgRow['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete from server
        }
        $conn->query("DELETE FROM gallery WHERE id=$id");
        echo "<div class='ui green message'>Image deleted successfully!</div>";
    }
}

// UPDATE IMAGE DESCRIPTION
if (isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $newDescription = $conn->real_escape_string($_POST['description']);
    $conn->query("UPDATE gallery SET description='$newDescription' WHERE id=$id");
    echo "<div class='ui green message'>Description updated successfully!</div>";
}

// UPLOAD NEW IMAGE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && !isset($_POST['update_id'])) {
    $description = $conn->real_escape_string($_POST['description']);
    $imageName = basename($_FILES['image']['name']);
    $targetPath = "../uploads/" . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $conn->query("INSERT INTO gallery (image, description) VALUES ('$imageName', '$description')");
        echo "<div class='ui green message'>Image uploaded successfully!</div>";
    } else {
        echo "<div class='ui red message'>Upload failed. Try again.</div>";
    }
}
?>

<div class="ui container">
    <?php include './admin_components/admin_top-menu.php'; ?>

    <div class="ui flex grid">
        <!-- Left menu -->
        <div class="four wide column">
            <?php include './admin_components/admin_side-menu.php'; ?>
        </div>

        <!-- Right content -->
        <div class="twelve wide column">
            <h2 class="ui header">Upload Gallery Image</h2>

            <!-- Upload Form -->
            <form class="ui form" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                <div class="field">
                    <label>Description</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>
                <button class="ui primary button" type="submit">Upload</button>
            </form>

            <h3 class="ui dividing header">Manage Gallery</h3>

            <!-- EDIT FORM IF edit_id IS SET -->
            <?php if (isset($_GET['edit_id'])): 
                $editId = intval($_GET['edit_id']);
                $editQuery = $conn->query("SELECT * FROM gallery WHERE id=$editId");
                if ($editQuery && $editQuery->num_rows > 0) {
                    $editData = $editQuery->fetch_assoc();
            ?>
            <form class="ui form" method="post">
                <input type="hidden" name="update_id" value="<?= $editData['id']; ?>">
                <div class="field">
                    <label>Edit Description for: <?= htmlspecialchars($editData['image']); ?></label>
                    <textarea name="description" rows="3"
                        required><?= htmlspecialchars($editData['description']); ?></textarea>
                </div>
                <button class="ui orange button" type="submit">Update</button>
                <a href="gallery.php" class="ui button">Cancel</a>
            </form>
            <div class="ui divider"></div>
            <?php } endif; ?>

            <!-- Gallery Table -->
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Uploaded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $gallery = $conn->query("SELECT * FROM gallery ORDER BY uploaded_at DESC");
                    if ($gallery && $gallery->num_rows > 0) {
                        while ($row = $gallery->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><img src='../uploads/{$row['image']}' width='120' alt='Gallery Image'></td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . $row['uploaded_at'] . "</td>";
                            echo "<td>
                                    <a href='gallery.php?edit_id={$row['id']}' class='ui tiny button'>Edit</a>
                                    <a href='gallery.php?delete_id={$row['id']}' class='ui tiny red button' onclick=\"return confirm('Are you sure?');\">Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No images found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include './admin_components/admin_footer.php'; ?>
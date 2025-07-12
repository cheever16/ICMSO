<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../db-connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = $_FILES['image']['name'];
    $target = "../img/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $query = "INSERT INTO gallery (image, description) VALUES ('$image', '$description')";
        if ($conn->query($query)) {
            $msg = "Image uploaded successfully.";
        } else {
            $msg = "Database error.";
        }
    } else {
        $msg = "Failed to upload image.";
    }
}
?>

<?php include './admin_components/admin_header.php'; ?>
<div class="ui container">
    <h2 class="ui header">Upload Gallery Image</h2>
    <?php if (isset($msg)) echo "<div class='ui message'>$msg</div>"; ?>
    <form class="ui form" method="POST" enctype="multipart/form-data">
        <div class="field">
            <label>Select Image</label>
            <input type="file" name="image" required>
        </div>
        <div class="field">
            <label>Description</label>
            <textarea name="description" rows="3" required></textarea>
        </div>
        <button class="ui primary button" type="submit">Upload</button>
        <a href="admin-dashboard.php" class="ui button">Cancel</a>
    </form>
</div>
<?php include './admin_components/admin_footer.php'; ?>
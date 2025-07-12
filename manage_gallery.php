<?php
include './components/db-connect.php';
include './components/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_dir = "./img/";
    $filename = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        mysqli_query($conn, "INSERT INTO gallery_images (filename) VALUES ('$filename')");
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = mysqli_query($conn, "SELECT filename FROM gallery_images WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    unlink('./img/' . $row['filename']);
    mysqli_query($conn, "DELETE FROM gallery_images WHERE id=$id");
    header("Location: manage_gallery.php");
    exit();
}
?>

<div class="ui container">
    <h2>Manage Gallery Images</h2>
    <form method="post" enctype="multipart/form-data" class="ui form">
        <div class="field">
            <label>Select Image to Upload</label>
            <input type="file" name="image" required>
        </div>
        <button class="ui primary button" type="submit">Upload Image</button>
    </form>

    <div class="ui grid">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM gallery_images ORDER BY uploaded_at DESC");
        while ($img = mysqli_fetch_assoc($result)) {
            echo '<div class="four wide column">';
            echo '<img src="./img/' . $img['filename'] . '" class="ui fluid image"><br>';
            echo '<a class="ui red button" href="?delete=' . $img['id'] . '" onclick="return confirm(\'Delete this image?\')">Delete</a>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<?php include './components/footer.php'; ?>

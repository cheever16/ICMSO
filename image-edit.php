<?php
include '../db-connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

$cid = $_GET['cid']; // Get child ID from URL

// Fetch child data for the given ID
$result = mysqli_query($conn, "SELECT * FROM children WHERE cid = $cid");
$child = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cname = $_POST['cname'];
    $cdob = $_POST['cdob'];
    $cyoe = $_POST['cyoe'];
    $cclass = $_POST['cclass'];

    // If a new photo is uploaded
    if (isset($_FILES['cphoto']) && $_FILES['cphoto']['error'] == 0) {
        $photo_name = $_FILES['cphoto']['name'];
        $photo_tmp_name = $_FILES['cphoto']['tmp_name'];
        $photo_target_dir = "uploads/";
        $photo_target_file = $photo_target_dir . basename($photo_name);

        // Check if the file is a valid image
        $image_check = getimagesize($photo_tmp_name);
        if ($image_check !== false) {
            // Delete the old image if it exists
            $old_photo = "uploads/" . $child['cphoto'];
            if (file_exists($old_photo)) {
                unlink($old_photo);
            }

            // Move the new photo to the target directory
            if (move_uploaded_file($photo_tmp_name, $photo_target_file)) {
                // Update child data with new photo
                $sql = "UPDATE children SET cname = '$cname', cdob = '$cdob', cyoe = '$cyoe', cclass = '$cclass', cphoto = '$photo_name' WHERE cid = $cid";
            } else {
                echo "Error uploading the image.";
            }
        }
    } else {
        // Update child data without changing the photo
        $sql = "UPDATE children SET cname = '$cname', cdob = '$cdob', cyoe = '$cyoe', cclass = '$cclass' WHERE cid = $cid";
    }

    if (mysqli_query($conn, $sql)) {
        echo "Child details updated successfully!";
        header("Location: children.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <div class="field">
        <label>Child Name</label>
        <input type="text" name="cname" value="<?php echo htmlspecialchars($child['cname']); ?>" required>
    </div>
    <div class="field">
        <label>Date of Birth</label>
        <input type="date" name="cdob" value="<?php echo $child['cdob']; ?>" required>
    </div>
    <div class="field">
        <label>Year Enrolled</label>
        <input type="text" name="cyoe" value="<?php echo $child['cyoe']; ?>" required>
    </div>
    <div class="field">
        <label>Class</label>
        <input type="text" name="cclass" value="<?php echo $child['cclass']; ?>" required>
    </div>
    <div class="field">
        <label>Upload New Photo (optional)</label>
        <input type="file" name="cphoto" accept="image/*">
    </div>
    <button class="ui primary button" type="submit">Update Child</button>
</form>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<?php include './admin_components/admin_header.php'; ?>
<?php include '../db-connection.php'; ?>

<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './admin_components/admin_top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui grid">
        <!-- Left menu -->
        <?php include './admin_components/admin_side-menu.php'; ?>

        <!-- right content -->
        <div class="twelve wide column">
            <h2 class="ui dividing header">Register New Child</h2>

            <?php
                if (isset($_POST['submit_child'])) {
                    $child_name = mysqli_real_escape_string($conn, $_POST['child_name']);
                    $child_dob = $_POST['child_dob'];
                    $child_yoe = (int)$_POST['child_yoe'];
                    $child_class = (int)$_POST['child_class'];
                    $child_story = mysqli_real_escape_string($conn, $_POST['child_story_behind']);
                    $child_health_status = mysqli_real_escape_string($conn, $_POST['child_health_status']);
                    $child_pic = $_FILES['child_pic']['name'];

                    // Handle file upload
                    if (!empty($child_pic)) {
                        $target_dir = "uploads/";
                        $target_file = $target_dir . basename($child_pic);
                        move_uploaded_file($_FILES["child_pic"]["tmp_name"], $target_file);
                    }

                    // Insert data into database
                    $sql = "INSERT INTO children (cname, cdob, cyoe, cclass, story, health_status, cphoto)
                            VALUES ('$child_name', '$child_dob', '$child_yoe', '$child_class', '$child_story', '$child_health_status', '$child_pic')";

if ($conn->query($sql) === TRUE) {
    // Calculate age from DOB
    $dob = new DateTime($child_dob);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    echo "<div class='ui positive message'>New child registered successfully! Estimated Age: {$age} years</div>";
}
 else {
                        echo "<div class='ui negative message'>Error: " . $conn->error . "</div>";
                    }
                }
            ?>

            <form action="" method="POST" class="ui form" enctype="multipart/form-data">
                <div class="field">
                    <label>Child's Full Name</label>
                    <input type="text" name="child_name" required placeholder="Enter full name">
                </div>

                <div class="two fields">
                    <div class="field">
                        <label>Date of Birth</label>
                        <input type="date" name="child_dob" required>
                    </div>
                    <div class="field">
                        <label>Year of Enrollment</label>
                        <input type="number" name="child_yoe" min="2000" max="2100" required>
                    </div>
                </div>

                <div class="field">
                    <label>Class / Grade</label>
                    <input type="number" name="child_class" min="1" max="12" required>
                </div>

                <div class="field">
                    <label>Inspiring Background Story</label>
                    <textarea name="child_story_behind" rows="3" placeholder="Write a short, inspiring story..."
                        required></textarea>
                </div>

                <div class="field">
                    <label>Health Status</label>
                    <input type="text" name="child_health_status" placeholder="e.g. Good, Fair, Needs Medical Attention"
                        required>
                </div>

                <div class="field">
                    <label>Upload Child Photo</label>
                    <input type="file" name="child_pic" accept="image/*">
                </div>

                <button class="ui primary button" name="submit_child" type="submit">Add Child</button>
                <button class="ui button" type="reset">Reset</button>
            </form>
        </div>
    </div>
</div>
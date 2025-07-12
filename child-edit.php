<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<?php include './admin_components/admin_header.php'; ?>
<?php include '../db-connection.php'; ?>

<style>
body {
    font-family: 'Arial', sans-serif;
    background-color: #f9fafb;
    color: #333;
}

.ui.container {
    margin-top: 3rem;
}

h2.ui.dividing.header {
    font-size: 2.5rem;
    color: #2c3e50;
    text-align: center;
    margin-bottom: 2rem;
}

.ui.grid {
    margin-top: 3rem;
}

.ui.grid .column {
    padding: 2rem;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.ui.form {
    margin-top: 1.5rem;
}

.ui.form .field {
    margin-bottom: 1.5rem;
}

.ui.form input,
.ui.form textarea {
    border-radius: 8px;
    padding: 10px;
    font-size: 1rem;
    width: 100%;
    border: 1px solid #ddd;
}

.ui.form input[type="date"],
.ui.form input[type="number"] {
    padding-left: 12px;
}

.ui.form label {
    font-size: 1.2rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.ui.message {
    border-radius: 8px;
    margin-top: 1.5rem;
}

.ui.primary.button {
    background-color: #0066cc;
    color: white;
    font-weight: bold;
}

.ui.button {
    background-color: #e74c3c;
    color: white;
    font-weight: bold;
}

.ui.button:hover,
.ui.primary.button:hover {
    transform: scale(1.05);
}

.ui.form input[type="file"] {
    margin-top: 1rem;
}

.ui.form img {
    border-radius: 8px;
    margin-top: 1rem;
}

.field .ui.button,
.field a.ui.button {
    margin-top: 1rem;
}
</style>

<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './admin_components/admin_top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui grid">
        <!-- Left menu -->
        <?php include './admin_components/admin_side-menu.php'; ?>

        <!-- Right content -->
        <div class="twelve wide column">
            <h2 class="ui dividing header">Edit Child Information</h2>

            <?php
            if (!isset($_GET['cid'])) {
                echo "<div class='ui warning message'>No child selected for editing.</div>";
                exit;
            }

            $cid = (int)$_GET['cid'];
            $result = $conn->query("SELECT * FROM children WHERE cid = $cid");

            if ($result->num_rows == 0) {
                echo "<div class='ui negative message'>Child not found.</div>";
                exit;
            }

            $child = $result->fetch_assoc();

            if (isset($_POST['update_child'])) {
                $child_name = mysqli_real_escape_string($conn, $_POST['child_name']);
                $child_dob = $_POST['child_dob'];
                $child_yoe = (int)$_POST['child_yoe'];
                $child_class = (int)$_POST['child_class'];
                $child_health = mysqli_real_escape_string($conn, $_POST['child_health_status']);
                $child_story_behind = mysqli_real_escape_string($conn, $_POST['child_story_behind']);
                $child_pic = $child['cphoto'];

                // If new photo uploaded
                if (!empty($_FILES['child_pic']['name'])) {
                    $new_pic = $_FILES['child_pic']['name'];
                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($new_pic);
                    if (move_uploaded_file($_FILES["child_pic"]["tmp_name"], $target_file)) {
                        $child_pic = $new_pic;
                    }
                }

                $sql = "UPDATE children SET 
                        cname = '$child_name',
                        cdob = '$child_dob',
                        cyoe = '$child_yoe',
                        cclass = '$child_class',
                        health_status = '$child_health',
                        cstory = '$child_story_behind',
                        cphoto = '$child_pic'
                        WHERE cid = $cid";

                if ($conn->query($sql) === TRUE) {
                    echo "<div class='ui positive message'>Child updated successfully!</div>";
                    $result = $conn->query("SELECT * FROM children WHERE cid = $cid");
                    $child = $result->fetch_assoc(); // Refresh data
                } else {
                    echo "<div class='ui negative message'>Error: " . $conn->error . "</div>";
                }
            }
            ?>

            <form action="" method="POST" class="ui form" enctype="multipart/form-data">
                <div class="field">
                    <label>Child's Full Name</label>
                    <input type="text" name="child_name" value="<?php echo htmlspecialchars($child['cname']); ?>"
                        required>
                </div>

                <div class="two fields">
                    <div class="field">
                        <label>Date of Birth</label>
                        <input type="date" name="child_dob" value="<?php echo $child['cdob']; ?>" required>
                    </div>
                    <?php
$dob = new DateTime($child['cdob']);
$today = new DateTime();
$current_age = $today->diff($dob)->y;
?>
                    <p><strong>Current Age:</strong> <?php echo $current_age; ?> years</p>

                    <div class="field">
                        <label>Year of Enrollment</label>
                        <input type="number" name="child_yoe" value="<?php echo $child['cyoe']; ?>" min="2000"
                            max="2100" required>
                    </div>
                </div>

                <div class="field">
                    <label>Class / Grade</label>
                    <input type="number" name="child_class" value="<?php echo $child['cclass']; ?>" min="1" max="12"
                        required>
                </div>

                <div class="field">
                    <label>Health Status</label>
                    <input type="text" name="child_health_status"
                        value="<?php echo htmlspecialchars($child['health_status']); ?>" required>
                </div>

                <div class="field">
                    <label>Inspiring Story</label>
                    <textarea name="child_story_behind" rows="3"
                        required><?php echo htmlspecialchars($child['cstory']); ?></textarea>
                </div>

                <div class="field">
                    <label>Current Photo</label><br>
                    <img src="uploads/<?php echo htmlspecialchars($child['cphoto']); ?>" style="max-width: 150px;">
                </div>

                <div class="field">
                    <label>Change Photo (Optional)</label>
                    <input type="file" name="child_pic" accept="image/*">
                </div>

                <button class="ui primary button" name="update_child" type="submit">Update Child</button>
                <a href="child-gallery-unsponsored.php" class="ui button">Cancel</a>
            </form>
        </div>
    </div>
</div>
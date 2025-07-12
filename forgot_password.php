<?php
include './components/db_connect.php';
include './components/header.php';
?>

<div class="ui container">
    <h2 class="pt-4">Reset Your Password</h2>

    <?php
    $msg = '';
    if (isset($_POST['reset'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

        if (!empty($username) && !empty($email) && !empty($new_password)) {
            $check = mysqli_query($conn, "SELECT * FROM member WHERE username='$username' AND email='$email'");
            if (mysqli_num_rows($check) == 1) {
                $update = "UPDATE member SET password=SHA('$new_password') WHERE username='$username' AND email='$email'";
                if (mysqli_query($conn, $update)) {
                    $msg = '<div class="ui positive message">Password updated successfully. <a href="login.php">Login now</a></div>';
                } else {
                    $msg = '<div class="ui negative message">Error updating password.</div>';
                }
            } else {
                $msg = '<div class="ui warning message">No matching user found with provided details.</div>';
            }
        } else {
            $msg = '<div class="ui warning message">Please fill all fields.</div>';
        }
    }

    echo $msg;
    ?>

    <form method="post" class="ui form">
        <div class="field">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="field">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <button name="reset" class="ui primary button">Reset Password</button>
    </form>
</div>

<?php include './components/footer.php'; ?>
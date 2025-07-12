<?php
session_start();
include './components/db_connect.php'; // DB connection

// Restore session from cookies (only if not already in session)
if (
    !isset($_SESSION['user_id']) &&
    isset($_COOKIE['user_id']) &&
    isset($_COOKIE['username']) &&
    isset($_COOKIE['role'])
) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['role'] = $_COOKIE['role'];
}

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ./admin/index.php");
    } else {
        header("Location: member_dashboard.php");
    }
    exit();
}

include './components/header.php';
?>

<div class="ui container">

    <?php 
    // Only show top menu if user is NOT logged in
    if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
        include './components/top-menu.php';
    }
    ?>

    <div class="ui centered grid">
        <h2 class="pt-4">Login Here</h2>
    </div>

    <div class="ui grid">
        <div class="six wide column centered">
            <?php
            $error_msg = "";

            if (isset($_POST['submit'])) {
                $user_username = mysqli_real_escape_string($conn, trim($_POST['username']));
                $user_password = mysqli_real_escape_string($conn, trim($_POST['password']));

                if (!empty($user_username) && !empty($user_password)) {
                    $query = "SELECT user_id, username, role FROM member WHERE username = '$user_username' AND password = SHA('$user_password')";
                    $data = mysqli_query($conn, $query);

                    if (mysqli_num_rows($data) == 1) {
                        $row = mysqli_fetch_array($data);
                        $_SESSION['user_id'] = $row['user_id'];
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['role'] = $row['role'];

                        // Set cookies for persistent login (30 days)
                        setcookie('user_id', $row['user_id'], time() + (86400 * 30), "/");
                        setcookie('username', $row['username'], time() + (86400 * 30), "/");
                        setcookie('role', $row['role'], time() + (86400 * 30), "/");

                        if ($row['role'] === 'admin') {
                            header("Location: ./admin/index.php");
                        } else {
                            header("Location: member_dashboard.php");
                        }
                        exit();
                    } else {
                        $error_msg = '<div class="ui warning message">Invalid Username or Password</div>';
                    }
                } else {
                    $error_msg = '<div class="ui warning message">Please enter Username and Password</div>';
                }
            }

            echo $error_msg;
            ?>

            <!-- Forgot Password Link -->
            <div style="text-align: center; padding: 10px;">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="ui form">
                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="User Name">
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password">
                </div>
                <div>Don't have an account? <a href="signup.php">Sign Up</a></div>
                <button name="submit" class="ui primary button" type="submit">Login</button>
            </form>
        </div>
    </div>

</div>

<?php include './components/footer.php'; ?>
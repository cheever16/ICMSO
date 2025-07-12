<?php include './components/header.php'; ?>
<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './components/top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui centered grid">
        <h2 class="pt-4">Sign Up Here</h2>
    </div>

    <div class="ui grid">
        <div class="six wide column centered">

            <?php
            include './components/db_connect.php';
            

            // Capture redirect target if provided
            $redirect_to = isset($_GET['redirect']) ? $_GET['redirect'] : null;

            if (isset($_POST['submit'])) {
                // Grab the profile data from the POST
                $username = mysqli_real_escape_string($conn, trim($_POST['username']));
                $email = mysqli_real_escape_string($conn, trim($_POST['email']));
                $password1 = mysqli_real_escape_string($conn, trim($_POST['password1']));
                $password2 = mysqli_real_escape_string($conn, trim($_POST['password2']));

                if (!empty($username) && !empty($email) && !empty($password1) && !empty($password2) && ($password1 == $password2)) {
                    // Make sure someone isn't already registered using this username
                    $query = "SELECT * FROM member WHERE username = '$username'";
                    $data = mysqli_query($conn, $query);

                    if (mysqli_num_rows($data) == 0) {
                        // The username is unique, insert data
                        $hashed_password = sha1($password1);
                        $query = "INSERT INTO member (username, email, password, role, join_date) VALUES ('$username', '$email', '$hashed_password', 'member', NOW())";
                        if (mysqli_query($conn, $query)) {
                            // Set session and cookies
                            $user_id = mysqli_insert_id($conn);
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['username'] = $username;
                            $_SESSION['role'] = 'member';

                            setcookie('user_id', $user_id, time() + (60 * 60 * 24 * 30));
                            setcookie('username', $username, time() + (60 * 60 * 24 * 30));

                            // Redirect based on target
                            switch ($redirect_to) {
                                case 'donation':
                                    header('Location: donation.php');
                                    break;
                                case 'sponsor':
                                    header('Location: sponsor-children.php');
                                    break;
                                case 'gift':
                                    header('Location: send-gift.php');
                                    break;
                                default:
                                    header('Location: member_dashboard.php');
                            }
                            exit();
                        } else {
                            echo '<div class="ui red message">Account creation failed. Please try again.</div>';
                        }
                    } else {
                        echo '<div class="ui warning message">An account already exists for this username.</div>';
                        $username = "";
                    }
                } else {
                    echo '<div class="ui warning message">Enter all sign-up data, including the email and matching passwords.</div>';
                }
            }
            ?>

            <!-- Sign Up Form -->
            <form method="post"
                action="signup.php<?php echo $redirect_to ? '?redirect=' . htmlspecialchars($redirect_to) : ''; ?>"
                class="ui form">
                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="User Name" required>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Email Address" required>
                </div>
                <div class="field">
                    <label for="password1">Password</label>
                    <input type="password" name="password1" id="password1" placeholder="Password" required>
                </div>
                <div class="field">
                    <label for="password2">Password - Retype</label>
                    <input type="password" name="password2" id="password2" placeholder="Retype Password" required>
                </div>
                <div>Already have an account?
                    <a
                        href="login.php<?php echo $redirect_to ? '?redirect=' . htmlspecialchars($redirect_to) : ''; ?>">Login</a>
                </div>
                <button name="submit" class="ui primary button" type="submit">Sign up</button>
            </form>

        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>
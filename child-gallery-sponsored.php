<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include './components/db_connect.php'; // Connect to DB

// Restore session from cookies if session is missing
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

// Require login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

include './components/header.php';
?>

<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './components/top-menu.php'; ?>

    <div class="ui stackable grid">
        <!-- Left menu -->
        <?php include './components/side-menu.php'; ?>

        <!-- Right content -->
        <div class="twelve wide column">
            <div class="ui raised segment">
                <h2 class="ui header"><i class="users icon"></i> Sponsored Children</h2>

                <div class="ui pointing secondary menu">
                    <a class="active item" href="child-gallery-sponsored.php">Sponsored Children</a>
                    <a class="item" href="child-gallery-unsponsored.php">Not Sponsored Children</a>
                </div>

                <?php
                $sql = "
                    SELECT c.cid, c.cname, c.cdob, c.cyoe, c.cclass, c.cphoto 
                    FROM children c
                    INNER JOIN sponsorer s ON c.cid = s.cid
                    WHERE c.sponsored = 1 AND s.confirmed = 1
                ";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $dob = $row["cdob"];
                        $age = date('Y') - date('Y', strtotime($dob));
                        $child_photo = $row["cphoto"] ? 'admin/uploads/' . $row["cphoto"] : 'img/defaultimg.png';
                ?>

                <div class="ui segment">
                    <div class="ui items">
                        <div class="item">
                            <div class="ui small image">
                                <img src="<?php echo htmlspecialchars($child_photo); ?>" alt="Child Photo">
                            </div>
                            <div class="content">
                                <div class="header"><?php echo htmlspecialchars($row["cname"]); ?></div>
                                <div class="meta">
                                    <span>Age: <?php echo htmlspecialchars($age); ?></span>
                                    <span>Class: <?php echo htmlspecialchars($row["cclass"]); ?></span>
                                    <span>Enrollment Year: <?php echo htmlspecialchars($row["cyoe"]); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    }
                } else {
                    echo "<div class='ui warning message'>No confirmed sponsored children found.</div>";
                }
                ?>

            </div>
        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>
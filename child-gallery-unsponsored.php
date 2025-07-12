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
                <h2 class="ui header"><i class="child icon"></i> Not Sponsored Children</h2>

                <div class="ui pointing secondary menu">
                    <a class="item" href="child-gallery-sponsored.php">Sponsored Children</a>
                    <a class="active item" href="child-gallery-unsponsored.php">Not Sponsored Children</a>
                </div>

                <?php
                $sql = "
                    SELECT c.cid, c.cname, c.cclass, c.cdob, c.cphoto, c.cstory 
                    FROM children c 
                    LEFT JOIN sponsorer s ON c.cid = s.cid AND s.confirmed = 1 
                    WHERE c.sponsored = 0 OR (c.sponsored = 1 AND s.confirmed IS NULL)
                ";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $dob = new DateTime($row["cdob"]);
                        $today = new DateTime();
                        $age = $today->diff($dob)->y;

                        $child_photo = $row["cphoto"] ? 'admin/uploads/' . htmlspecialchars($row["cphoto"]) : 'img/defaultimg.png';
                        $story = !empty($row['cstory']) ? htmlspecialchars($row['cstory']) : "A bright and promising child looking for support.";
                ?>

                <div class="ui segment">
                    <div class="ui items">
                        <div class="item">
                            <div class="ui small image">
                                <img src="<?php echo $child_photo; ?>" alt="Child Photo">
                            </div>
                            <div class="content">
                                <div class="header"><?php echo htmlspecialchars($row["cname"]); ?></div>
                                <div class="meta">
                                    <span>Age: <?php echo htmlspecialchars($age); ?> years</span>
                                    <span>Class: <?php echo htmlspecialchars($row["cclass"]); ?></span>
                                </div>
                                <div class="description">
                                    <p><em>"<?php echo $story; ?>"</em></p>
                                </div>
                                <div class="extra">
                                    <a class="ui teal button" href="donation.php">Donate</a>
                                    <a class="ui blue button"
                                        href="sponsor-children.php?cid=<?php echo htmlspecialchars($row['cid']); ?>">Sponsor</a>
                                    <!-- Send a Gift button removed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    }
                } else {
                    echo "<div class='ui warning message'>No unsponsored children found.</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>

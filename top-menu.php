<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Top Navigation Bar -->
<div class="ui black inverted fixed menu" style="z-index: 1000;">
    <div class="ui container">
        <div class="header item">
            GOOD HOPE HOME
        </div>

        <a class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/index.php" ? "active" : ""); ?>" href="index.php">
            Home
        </a>

        <div class="right menu">
            <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Not Logged In -->
            <a href="login.php"
                class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/login.php" ? "active" : ""); ?>">
                Login
            </a>
            <?php else: ?>
            <!-- Logged In -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin/index.php"
                class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/admin/index.php" ? "active" : ""); ?>">
                Admin Panel
            </a>
            <?php endif; ?>
            <a href="logout.php" class="item">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Optional: CSS Styling -->
<style>
.ui.menu .item {
    font-size: 1.1em;
    padding: 10px 15px;
}

.ui.menu .item:hover {
    background-color: #333;
    transition: background-color 0.3s ease;
}

.ui.menu .active.item {
    background-color: #2c3e50 !important;
}

.ui.container {
    padding: 0 !important;
}

@media (max-width: 768px) {
    .ui.menu .item {
        padding: 10px;
    }
}
</style>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'member') {
    header('Location: login.php');
    exit;
}
?>

<?php include './components/header.php'; ?>

<!-- Page Content -->
<div class="ui container">
    <?php include './components/top-menu.php'; ?>

    <div class="ui segment">
        <h2 class="ui header">
            <i class="user circle outline icon"></i>
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </h2>
        <p class="ui message info">
            You are logged in as a <strong>member</strong>. You can view orphanage information but cannot make changes.
        </p>
    </div>
</div>

<?php include './components/footer.php'; ?>
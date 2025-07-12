<?php
// Check if session is already started, if not, start it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Define base path for consistent file inclusion
$basePath = __DIR__;

// Include admin header
include_once($basePath . '/admin_components/admin_header.php');
?>

<style>
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f7fc;
    color: #333;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.ui.container {
    margin-top: 2rem;
    flex: 1;
}

h1 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 1rem;
}

p {
    font-size: 1.1rem;
    color: #7f8c8d;
}

.ui.grid {
    display: flex;
    flex-wrap: wrap;
    margin-top: 3rem;
}

.ui.grid .column {
    padding: 2rem;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    margin-top: 2rem;
}

.ui.grid .column h1 {
    font-size: 2rem;
    color: #34495e;
    text-align: center;
}

.ui.grid .column p {
    font-size: 1.2rem;
    text-align: center;
    color: #95a5a6;
}

.twelve.wide.column.admin-welcome {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 300px;
}

.twelve.wide.column.admin-welcome h1 {
    font-size: 2.5rem;
    color: #1b1c1d;
}

.twelve.wide.column.admin-welcome p {
    font-size: 1.2rem;
    color: #666;
}

.ui.grid .column:first-child {
    flex: 1;
    min-width: 250px;
}

.ui.grid .column:last-child {
    flex: 3;
}

@media (max-width: 768px) {
    .ui.grid {
        flex-direction: column;
    }

    .ui.grid .column {
        width: 100%;
    }
}
</style>

<div class="ui container">
    <!-- Top Navigation Bar -->
    <?php include_once($basePath . '/admin_components/admin_top-menu.php'); ?>

    <!-- BODY Content -->
    <div class="ui grid">
        <!-- Left menu -->
        <?php include_once($basePath . '/admin_components/admin_side-menu.php'); ?>

        <!-- Right content -->
        <div class="twelve wide column" style="padding: 3rem; background-color: #ffffff;">
            <h1 class="ui header" style="font-size: 2.5rem; color: #1b1c1d;">Welcome, Administrator!</h1>
            <p style="font-size: 1.2rem; color: #666;">Manage the orphanage system from this dashboard.</p>
        </div>
    </div>
</div>

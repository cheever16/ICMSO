<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Correctly include db-connection.php from two levels up
include_once(__DIR__ . '/../../db-connection.php');

// If the session variables aren't set, try to initialize them from cookies
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
    }
}

// If still not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    echo '<div class="ui container"><div class="ui message"><p>Please <a href="../login.php">log in</a> to access this page.</p></div></div>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOOD HOPE HOME</title>

    <!-- Semantic UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>

    <!-- Custom styles -->
    <link rel="stylesheet" href="../css/main.css">

    <style>
    body {
        margin: 0;
        background-color: #f4f4f4;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
    }

    /* Static Sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 220px;
        height: 100vh;
        background-color: #ffffff;
        border-right: 1px solid #ddd;
        padding-top: 20px;
        z-index: 10;
    }

    /* Main Content Area */
    .main-content {
        margin-left: 220px;
        padding: 2em;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        box-sizing: border-box;
    }

    /* Sticky Footer */
    footer {
        background-color: #1b1c1d;
        color: #ffffff;
        text-align: center;
        padding: 0.8em;
        font-size: 0.85em;
        margin-top: auto;
        position: sticky;
        bottom: 0;
        width: 100%;
    }

    .ui.message {
        background-color: #ff4d4d;
        color: white;
        border-radius: 5px;
        padding: 15px;
        margin-top: 20px;
    }

    a {
        color: #2185d0;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>



</html>
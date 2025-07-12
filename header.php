<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Correctly include db-connection.php from the root directory
include __DIR__ . '/../db-connection.php'; // Goes up one level to the root directory

// If the session vars aren't set, try to set them with a cookie
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Orphanage Foundation</title>

    <!-- Link to Semantic UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
    <link rel="stylesheet" href="./css/main.css">

    <style>
    body {
        background-color: #f7f7f7;
        /* Light background for a clean look */
    }

    .ui.container {
        padding-top: 20px;
    }

    .header {
        color: #3c3c3c;
    }
    </style>
</head>

<body>
    <div class="ui container">
        <!-- Main Header -->
        <h1 class="ui header center aligned">Welcome to the Orphanage Foundation</h1>


    </div>
</body>

</html>
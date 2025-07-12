<?php include './components/header.php'; ?>

<div class="ui container">

    <!-- Top Navigation Bar -->
    <?php include './components/top-menu.php'; ?>

    <!-- Main Grid Layout -->
    <div class="ui stackable grid">
        <!-- Sidebar -->
        <?php include './components/side-menu.php'; ?>

        <!-- Main Content Area -->
        <div class="twelve wide column">
            <div class="ui raised very padded text container segment">
                <h2 class="ui header">Share Your Feedback</h2>

                <?php
                include 'db-connection.php';

                if (isset($_POST['submit_feedback'])) {
                    $comment = trim($_POST['comment']);

                    if (!empty($comment)) {
                        $stmt = $conn->prepare("INSERT INTO feedback (comment) VALUES (?)");
                        $stmt->bind_param("s", $comment);

                        if ($stmt->execute()) {
                            echo "<div class='ui green message'>Thank you! Your comment has been submitted.</div>";
                        } else {
                            echo "<div class='ui red message'>An error occurred while submitting your comment.</div>";
                        }

                        $stmt->close();
                    } else {
                        echo "<div class='ui red message'>Comment field cannot be empty.</div>";
                    }

                    $conn->close();
                }
                ?>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="ui form">
                    <div class="field">
                        <label for="comment">Your Comment</label>
                        <textarea id="comment" name="comment" rows="5" placeholder="Write your thoughts here..."
                            required></textarea>
                    </div>
                    <button type="submit" name="submit_feedback" class="ui primary button">
                        <i class="send icon"></i> Submit
                    </button>
                    <button type="reset" class="ui button">
                        <i class="redo icon"></i> Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include './admin_components/admin_header.php';
include '../db-connection.php';

// === Allocation logic is better run on donation confirmation or cron job ===
// For admin dashboard, just display resources with unit price and allow CRUD

// Handle resource submission with unit_price included
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_resource'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $quantity = (int)$_POST['quantity'];
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $unit_price = floatval($_POST['unit_price']);

    $sql = "INSERT INTO resources (item_name, quantity_needed, description, status, unit_price) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissd", $item_name, $quantity, $desc, $status, $unit_price);

    if ($stmt->execute()) {
        echo "<script>alert('New resource added successfully');</script>";
    } else {
        echo "<script>alert('Error adding resource: " . $conn->error . "');</script>";
    }

    $stmt->close();
}
?>

<div class="ui container">
    <!-- Top Navigation Bar -->
    <?php include './admin_components/admin_top-menu.php'; ?>

    <!-- Body Content -->
    <div class="ui flex grid">
        <!-- Left menu -->
        <div class="four wide column">
            <?php include './admin_components/admin_side-menu.php'; ?>
        </div>

        <!-- Right content -->
        <div class="twelve wide column">
            <h1>Create New Resource Entry</h1>

            <!-- Resource Submission Form -->
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="ui form">
                <div class="field">
                    <label>Item Name</label>
                    <input type="text" name="item_name" placeholder="e.g., Beds, Notebooks, Shoes" required>
                </div>

                <div class="field">
                    <label>Quantity Needed</label>
                    <input type="number" name="quantity" min="1" placeholder="e.g., 50" required>
                </div>

                <div class="field">
                    <label>Unit Price (per item in currency)</label>
                    <input type="number" name="unit_price" step="0.01" min="0" placeholder="e.g., 10.50" required>
                </div>

                <div class="field">
                    <label>Description</label>
                    <textarea name="desc" rows="2" placeholder="Short description (optional)"></textarea>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status" class="ui dropdown">
                        <option value="Needed">Needed</option>
                        <option value="Available">Available</option>
                    </select>
                </div>

                <button name="submit_resource" type="submit" class="ui primary button">Submit</button>
                <button type="reset" class="ui button">Reset</button>
            </form>

            <h2 class="ui dividing header">All Resources</h2>

            <!-- Resources Table -->
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Needed</th>
                        <th>Unit Price</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th> <!-- Action Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM resources ORDER BY id DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_name']); ?></td>
                        <td><?= (int)$row['quantity_needed']; ?></td>
                        <td><?= number_format($row['unit_price'], 2); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td>
                            <a class="ui blue mini button" href="edit-resource.php?id=<?= $row['id']; ?>">Edit</a>
                            <a class="ui red mini button" href="delete-resource.php?id=<?= $row['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this resource?');">Delete</a>
                        </td>
                    </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' class='center aligned'>No resources found.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


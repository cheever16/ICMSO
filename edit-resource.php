<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include './admin_components/admin_header.php';
include '../db-connection.php';

// Get resource ID from URL, sanitize to int
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing resource data securely
$stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$resource = $result->fetch_assoc();
$stmt->close();

if (!$resource) {
    echo "<p>Resource not found.</p>";
    exit;
}

// Update resource if form submitted
if (isset($_POST['update_resource'])) {
    $item_name = $_POST['item_name'];
    $quantity = (int)$_POST['quantity'];
    $desc = $_POST['desc'];
    $status = $_POST['status'];
    $unit_price = floatval($_POST['unit_price']);  // make sure this is a float

    // Use prepared statement to update
    $update = $conn->prepare("UPDATE resources SET 
                item_name = ?, 
                quantity_needed = ?, 
                description = ?, 
                status = ?, 
                unit_price = ? 
            WHERE id = ?");
    $update->bind_param("sissdi", $item_name, $quantity, $desc, $status, $unit_price, $id);

    if ($update->execute()) {
        echo "<script>alert('Resource updated successfully'); window.location.href='programs.php';</script>";
    } else {
        echo "<script>alert('Error updating resource');</script>";
    }

    $update->close();
}
?>

<div class="ui container">
    <?php include './admin_components/admin_top-menu.php'; ?>
    <div class="ui grid">
        <?php include './admin_components/admin_side-menu.php'; ?>
        <div class="twelve wide column">
            <h2>Edit Resource</h2>
            <form action="" method="post" class="ui form">
                <div class="field">
                    <label>Item Name</label>
                    <input type="text" name="item_name" value="<?= htmlspecialchars($resource['item_name']); ?>" required>
                </div>
                <div class="field">
                    <label>Quantity Needed</label>
                    <input type="number" name="quantity" min="1" value="<?= (int)$resource['quantity_needed']; ?>" required>
                </div>
                <div class="field">
                    <label>Unit Price</label>
                    <input type="number" step="0.01" min="0" name="unit_price" value="<?= number_format((float)$resource['unit_price'], 2, '.', ''); ?>" required>
                </div>
                <div class="field">
                    <label>Description</label>
                    <textarea name="desc" rows="2"><?= htmlspecialchars($resource['description']); ?></textarea>
                </div>
                <div class="field">
                    <label>Status</label>
                    <select name="status" class="ui dropdown">
                        <option value="Needed" <?= $resource['status'] == 'Needed' ? 'selected' : ''; ?>>Needed</option>
                        <option value="Available" <?= $resource['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                    </select>
                </div>
                <button name="update_resource" type="submit" class="ui primary button">Update</button>
                <a href="programs.php" class="ui button">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include './admin_components/admin_footer.php'; ?>

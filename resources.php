<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once './components/db_connect.php'; // Database connection

// Restore session from cookies
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];

    // Securely get user role
    $uid = mysqli_real_escape_string($conn, $_COOKIE['user_id']);
    $query = "SELECT role FROM member WHERE user_id = '$uid'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['role'] = $row['role'];
    }
}

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// === START General Donation Allocation Logic ===
// Get all confirmed general donations
$donationQuery = mysqli_query($conn, "
    SELECT d_id, amount 
    FROM donation 
    WHERE program = 'general donation' 
      AND status = 'Confirmed'
");

while ($donation = mysqli_fetch_assoc($donationQuery)) {
    $donation_id = $donation['d_id'];
    $remaining_amount = $donation['amount'];

    // Check already allocated amount
    $allocatedResult = mysqli_query($conn, "
        SELECT SUM(allocated_amount) AS total_allocated 
        FROM general_donation_allocation 
        WHERE donation_id = $donation_id
    ");
    $allocatedRow = mysqli_fetch_assoc($allocatedResult);
    $already_allocated = $allocatedRow['total_allocated'] ?? 0;

    $remaining_amount -= $already_allocated;
    if ($remaining_amount <= 0) continue; // Skip fully allocated donations

    // Fetch needed resources ordered by id (or priority)
    $resourceQuery = mysqli_query($conn, "
        SELECT * FROM resources 
        WHERE status = 'Needed' AND quantity_needed > 0 
        ORDER BY id ASC
    ");

    while ($resource = mysqli_fetch_assoc($resourceQuery)) {
        $resource_id = $resource['id'];
        $unit_price = $resource['unit_price'] ?? 0;
        $quantity_needed = $resource['quantity_needed'];

        if ($unit_price <= 0) continue;

        // Calculate max units that can be fulfilled
        $max_units = floor($remaining_amount / $unit_price);
        if ($max_units <= 0) continue;

        $allocated_units = min($max_units, $quantity_needed);
        $allocation_amount = $allocated_units * $unit_price;

        // Insert allocation record
        mysqli_query($conn, "
            INSERT INTO general_donation_allocation (donation_id, resource_id, allocated_amount, allocated_quantity) 
            VALUES ($donation_id, $resource_id, $allocation_amount, $allocated_units)
        ");

        // Update resource quantities and status
        $new_quantity = $quantity_needed - $allocated_units;
        $status = ($new_quantity <= 0) ? 'Available' : 'Needed';

        mysqli_query($conn, "
            UPDATE resources 
            SET quantity_needed = $new_quantity, status = '$status' 
            WHERE id = $resource_id
        ");

        $remaining_amount -= $allocation_amount;
        if ($remaining_amount <= 0) break;
    }
}
// === END Allocation Logic ===

include './components/header.php';
?>

<div class="ui container">

    <!-- Top Navigation -->
    <?php include './components/top-menu.php'; ?>

    <div class="ui stackable grid">
        <!-- Side Menu -->
        <?php include './components/side-menu.php'; ?>

        <!-- Main Content -->
        <div class="twelve wide column">
            <div class="ui raised very padded segment">
                <h2 class="ui header"><i class="clipboard list icon"></i> Resources Needed</h2>

                <table class="ui celled striped table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Unit Price</th>
                            <th>Quantity Needed</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // ✅ Only show resources that are still needed
                        $sql = "SELECT * FROM resources WHERE quantity_needed > 0 AND status = 'Needed' ORDER BY item_name ASC";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                echo "<td>" . number_format($row['unit_price'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['quantity_needed']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                echo "<td>" . ucfirst(htmlspecialchars($row['status'])) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='center aligned'>No resources currently needed.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
include './components/footer.php'; 
$conn->close();
?>

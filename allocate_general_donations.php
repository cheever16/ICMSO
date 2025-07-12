<?php
include 'config.php'; // database connection

// 1. Get all confirmed general donations that haven't been fully allocated
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

    // 2. Fetch needed resources ordered by priority (you can customize this)
    $resourceQuery = mysqli_query($conn, "
        SELECT * FROM resources 
        WHERE status = 'Needed' AND quantity_needed > 0 
        ORDER BY id ASC
    ");

    while ($resource = mysqli_fetch_assoc($resourceQuery)) {
        $resource_id = $resource['id'];
        $unit_price = $resource['unit_price'];
        $quantity_needed = $resource['quantity_needed'];

        if ($unit_price <= 0) continue;

        // Calculate how many units can be fulfilled
        $max_units = floor($remaining_amount / $unit_price);
        if ($max_units <= 0) continue;

        $allocated_units = min($max_units, $quantity_needed);
        $allocation_amount = $allocated_units * $unit_price;

        // 3. Insert into allocation table
        mysqli_query($conn, "
            INSERT INTO general_donation_allocation (donation_id, resource_id, allocated_amount, allocated_quantity) 
            VALUES ($donation_id, $resource_id, $allocation_amount, $allocated_units)
        ");

        // 4. Update resource
        $new_quantity = $quantity_needed - $allocated_units;
        $status = ($new_quantity <= 0) ? 'Available' : 'Needed';

        mysqli_query($conn, "
            UPDATE resources 
            SET quantity_needed = $new_quantity, status = '$status' 
            WHERE id = $resource_id
        ");

        // Reduce remaining general donation amount
        $remaining_amount -= $allocation_amount;

        if ($remaining_amount <= 0) break; // move to next donation
    }
}

echo "✅ General donations allocated successfully.";
?>

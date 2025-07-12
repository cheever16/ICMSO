<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<?php include './admin_components/admin_header.php'; ?>
<?php include '../db-connection.php'; ?>

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
    text-align: center;
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

.ui.table {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-top: 2rem;
}

.ui.table th {
    background-color: #f1f1f1;
    color: #2c3e50;
}

.ui.table td {
    color: #34495e;
}

.ui.table .ui.button {
    margin: 5px;
}

.ui.message {
    margin-top: 20px;
    border-radius: 8px;
}

.ui.button:hover {
    transform: scale(1.05);
}

.ui.primary.button {
    background-color: #0066cc;
    color: white;
    font-weight: bold;
}

.ui.red.button {
    background-color: #e74c3c;
    color: white;
}

.ui.blue.button {
    background-color: #3498db;
    color: white;
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
    <?php include './admin_components/admin_top-menu.php'; ?>

    <!-- BODY Content -->
    <div class="ui grid">
        <!-- Left menu -->
        <?php include './admin_components/admin_side-menu.php'; ?>

        <!-- Right content -->
        <div class="twelve wide column">
            <h1>Children - Orphan Management</h1>

            <?php
                // Delete child record if ID is provided
                if (isset($_GET['delete_id'])) {
                    $child_id = intval($_GET['delete_id']);

                    // Fetch photo to remove from uploads
                    $photo_query = $conn->query("SELECT cphoto FROM children WHERE cid = $child_id");
                    if ($photo_query && $photo_query->num_rows > 0) {
                        $photo_data = $photo_query->fetch_assoc();
                        $photo_path = "uploads/" . $photo_data['cphoto'];
                        if (!empty($photo_data['cphoto']) && file_exists($photo_path)) {
                            unlink($photo_path);
                        }
                    }

                    // Delete child
                    $delete_query = $conn->query("DELETE FROM children WHERE cid = $child_id");
                    if ($delete_query) {
                        echo "<div class='ui positive message'>Child record deleted successfully.</div>";
                    } else {
                        echo "<div class='ui negative message'>Failed to delete the child.</div>";
                    }
                }
            ?>

            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>CID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Year Enrolled</th>
                        <th>Class</th>
                        <th>Photo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM children";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $formateddate = date("d-m-Y", strtotime($row['cdob']));
                                echo "<tr>";
                                echo "<td>{$row['cid']}</td>";
                                echo "<td>{$row['cname']}</td>";
                                echo "<td>{$formateddate}</td>";
                                echo "<td>{$row['cyoe']}</td>";
                                echo "<td>{$row['cclass']}</td>";
                                echo "<td>";
                                if (!empty($row['cphoto'])) {
                                    echo "<img src='uploads/{$row['cphoto']}' width='50'>";
                                } else {
                                    echo "N/A";
                                }
                                echo "</td>";
                                echo "<td>
                                        <a href='child-edit.php?cid={$row['cid']}' class='ui blue mini button'>Edit</a>
                                        <a href='?delete_id={$row['cid']}' class='ui red mini button' onclick=\"return confirm('Are you sure you want to delete this child?');\">Delete</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No children found.</td></tr>";
                        }
                    ?>
                </tbody>
                <tfoot class="full-width">
                    <tr>
                        <th colspan="7">
                            <a class="ui primary button" href="children-add.php">Add Child</a>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
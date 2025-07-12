<?php include './components/header.php'; ?>

<div class="ui container" style="margin-top: 2rem;">
    <?php include './components/top-menu.php'; ?>

    <div class="ui stackable grid">
        <?php include './components/side-menu.php'; ?>

        <div class="twelve wide column">
            <h2 class="ui teal header">
                <i class="heart icon"></i>
                <div class="content">Sponsor this Child</div>
            </h2>

            <?php
            include './db-connection.php';

            if (!isset($_GET['cid']) || empty($_GET['cid'])) {
                echo "<script>alert('Invalid Child ID'); window.location.href = './child-gallery-sponsored.php';</script>";
                exit();
            }

            $cid = $_GET['cid'];

            // Handle Form Submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
                // Sanitize and validate inputs
                $noofyear = trim($_POST['noofyear']);
                $custom_time = trim($_POST['custom_time']);
                $firstname = trim($_POST['firstname']);
                $lastname = trim($_POST['lastname']);
                $email = trim($_POST['email']);
                $phone = trim($_POST['phone']);
                $address = trim($_POST['address']);
                $amount = $_POST['amount'];
                $sponsorship_type = $_POST['sponsorship_type'];

                $duration = !empty($noofyear) ? $noofyear : $custom_time;

                if (empty($duration)) {
                    echo "<script>alert('Please specify duration or custom time.');</script>";
                } elseif (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== 0) {
                    echo "<script>alert('File upload error or no file selected.');</script>";
                } else {
                    $file = $_FILES['payment_proof'];
                    $file_name = $file['name'];
                    $file_tmp = $file['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed = ['pdf'];

                    if (!in_array($file_ext, $allowed)) {
                        echo "<script>alert('Only PDF files are allowed.');</script>";
                    } elseif ($file['size'] > 5 * 1024 * 1024) {
                        echo "<script>alert('File exceeds 5MB.');</script>";
                    } else {
                        $new_file_name = uniqid('proof_', true) . '.' . $file_ext;
                        $upload_dir = 'admin/uploads/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                        $upload_path = $upload_dir . $new_file_name;

                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            $stmt = $conn->prepare("INSERT INTO sponsorer 
                                (spn_firstname, spn_lastname, spnd_date, spn_noofyears, spn_email, spn_phone, spn_bill_address, spn_amount, spn_sponsorship_type, cid, payment_proof) 
                                VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssssdsss", $firstname, $lastname, $duration, $email, $phone, $address, $amount, $sponsorship_type, $cid, $new_file_name);

                            $stmt2 = $conn->prepare("INSERT INTO sponsor_proofs (cid, sponsor_email, proof_file, uploaded_at) VALUES (?, ?, ?, NOW())");
                            $stmt2->bind_param("sss", $cid, $email, $new_file_name);

                            $stmt3 = $conn->prepare("UPDATE children SET sponsored = 1 WHERE cid = ?");
                            $stmt3->bind_param("s", $cid);

                            if ($stmt->execute() && $stmt2->execute() && $stmt3->execute()) {
                                echo "<script>alert('Sponsorship registration successful!'); window.location.href='./child-gallery-unsponsored.php';</script>";
                                exit();
                            } else {
                                echo "<script>alert('Database error occurred.');</script>";
                            }

                            $stmt->close();
                            $stmt2->close();
                            $stmt3->close();
                        } else {
                            echo "<script>alert('File upload failed.');</script>";
                        }
                    }
                }
                $conn->close();
            }
            ?>

            <!-- Sponsorship Form -->
            <form class="ui form segment"
                action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?cid=' . urlencode($cid); ?>" method="post"
                enctype="multipart/form-data">

                <!-- Child Info -->
                <h4 class="ui dividing header">Child's Details</h4>
                <?php
                include './db-connection.php';
                $stmt = $conn->prepare("SELECT cid, cname, cdob, cyoe, cclass, cphoto FROM children WHERE cid = ?");
                $stmt->bind_param("s", $cid);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $child = $result->fetch_assoc();
                    $dob = $child['cdob'];
                    $age = date('Y') - date('Y', strtotime($dob));
                    $photo = $child['cphoto'] ? 'admin/uploads/' . htmlspecialchars($child['cphoto']) : 'img/defaultimg.png';
                ?>
                <div class="ui items">
                    <div class="item">
                        <div class="ui small image">
                            <img src="<?php echo $photo; ?>" alt="Child Photo">
                        </div>
                        <div class="content">
                            <div class="header"><?php echo htmlspecialchars($child["cname"]); ?></div>
                            <div class="meta">
                                <span>Age: <?php echo $age; ?></span> |
                                <span>Class: <?php echo htmlspecialchars($child["cclass"]); ?></span> |
                                <span>Year of Enrollment: <?php echo htmlspecialchars($child["cyoe"]); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                } else {
                    echo "<script>alert('Child not found.'); window.location.href='./child-gallery-sponsored.php';</script>";
                    exit();
                }
                $stmt->close();
                ?>

                <!-- Duration -->
                <h4 class="ui dividing header">Time of Sponsorship</h4>
                <div class="two fields">
                    <div class="field">
                        <label>Number of Years</label>
                        <select name="noofyear" class="ui dropdown">
                            <option value="">Select Years</option>
                            <option value="1">1 Year</option>
                            <option value="2">2 Years</option>
                            <option value="3">3 Years</option>
                            <option value="5">5 Years</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Custom Time Interval (in Days)</label>
                        <input type="number" name="custom_time" min="1" placeholder="e.g. 90">
                        <small class="ui tiny text">Leave empty if you selected number of years above.</small>
                    </div>
                </div>

                <!-- Sponsorship Type -->
                <div class="field">
                    <label>Sponsorship Type</label>
                    <select name="sponsorship_type" class="ui dropdown" required>
                        <option value="">Select Type</option>
                        <option value="Education">Education</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Food">Food</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Personal Info -->
                <h4 class="ui dividing header">Personal Information</h4>
                <div class="two fields">
                    <div class="field">
                        <label>First Name</label>
                        <input type="text" name="firstname" placeholder="First Name" required>
                    </div>
                    <div class="field">
                        <label>Last Name</label>
                        <input type="text" name="lastname" placeholder="Last Name">
                    </div>
                </div>
                <div class="two fields">
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="field">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="Phone Number" required>
                    </div>
                </div>
                <div class="field">
                    <label>Billing Address</label>
                    <input type="text" name="address" placeholder="Full Address" required>
                </div>

                <!-- Bank Info -->
                <h4 class="ui dividing header">Bank Details</h4>
                <div class="ui segment">
                    <p><strong>Account Name:</strong> ELISANTE E SIMA</p>
                    <p><strong>Bank:</strong> NMB Bank</p>
                    <p><strong>Account Number:</strong> 43210027689</p>
                    <p><strong>Location:</strong> Arusha, Tanzania</p>
                    <p><strong>Contact:</strong> +255 766 575 400 | ellydecheever16@gmail.com</p>
                </div>

                <!-- Amount -->
                <div class="field">
                    <label>Amount (TShs)</label>
                    <input type="number" name="amount" min="1" placeholder="Enter Amount" required>
                </div>

                <!-- Upload -->
                <h4 class="ui dividing header">Proof of Payment</h4>
                <div class="field">
                    <label>Upload (PDF only)</label>
                    <input type="file" name="payment_proof" accept=".pdf" required>
                </div>

                <!-- Submit -->
                <button class="ui green button" name="submit" type="submit">
                    <i class="check icon"></i> Submit Sponsorship
                </button>
            </form>
        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>
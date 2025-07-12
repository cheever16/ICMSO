<?php include './components/header.php'; ?>

<div class="ui container">
    <!-- Top Navigation Bar -->
    <?php include './components/top-menu.php'; ?>

    <!-- Main Grid -->
    <div class="ui grid">
        <!-- Left menu -->
        <?php include './components/side-menu.php'; ?>

        <!-- Right content -->
        <div class="twelve wide column">
            <h1>Contact Us</h1>

            <p><strong>Address:</strong><br>
                GOOD HOPE HOME - Usariver, ARUSHA<br>
                Momela Road,<br>
                ARUSHA, TANZANIA</p>

            <p><strong>Email:</strong> ellydecheever16@gmail.com</p>
            <p><strong>Tel:</strong> +255766575400, +255765875400</p>

            <div class="ui divider"></div>

            <!-- Button to reveal the child registration form -->
            <button id="showFormButton" class="ui primary button">Request Child Registration</button>

            <!-- Child Registration Form -->
            <div id="childRegistrationForm" style="display: none; margin-top: 20px;">
                <h2>Child Registration Form</h2>

                <?php
                include 'db-connection.php';

                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_child'])) {
                    $child_name = $_POST['child_name'];
                    $child_dob = $_POST['child_dob'];
                    $child_yoe = $_POST['child_yoe'];
                    $child_class = $_POST['child_class'];
                    $child_health_status = $_POST['child_health_status'];
                    $child_story_behind = $_POST['child_story_behind'];
                    $child_health_description = $_POST['child_health_description'];
                    $email = $_POST['email'];
                    $child_pdf = $_FILES['child_pdf']['name'];

                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($_FILES["child_pdf"]["name"]);
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    if ($file_type !== "pdf") {
                        echo "<script>alert('Only PDF files are allowed for birth certificate upload.');</script>";
                    } else {
                        if (move_uploaded_file($_FILES["child_pdf"]["tmp_name"], $target_file)) {
                            $stmt = $conn->prepare("INSERT INTO child_registration_requests 
                                (cname, cdob, cyoe, cclass, chealth_status, cstory, chealth_description, cpdf, email) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssissssss", $child_name, $child_dob, $child_yoe, $child_class,
                                $child_health_status, $child_story_behind, $child_health_description,
                                $child_pdf, $email);

                            if ($stmt->execute()) {
                                echo "<script>alert('Registration request submitted successfully.');</script>";
                            } else {
                                echo "<script>alert('Error in submission.');</script>";
                            }
                            $stmt->close();
                        } else {
                            echo "<script>alert('File upload failed.');</script>";
                        }
                    }
                    $conn->close();
                }
                ?>

                <form action="" method="post" class="ui form" enctype="multipart/form-data"
                    onsubmit="return validateForm()">
                    <div class="field">
                        <label>Your Email Address</label>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="field">
                        <label>Child Name</label>
                        <input type="text" name="child_name" placeholder="Child's Name" required>
                    </div>

                    <div class="field">
                        <label>Date of Birth</label>
                        <input type="date" name="child_dob" required>
                    </div>

                    <div class="field">
                        <label>Year of Enroll</label>
                        <input type="number" min="1900" max="2200" name="child_yoe" required>
                    </div>

                    <div class="field">
                        <label>Class / Grade</label>
                        <input type="number" min="1" max="12" name="child_class" required>
                    </div>

                    <div class="field">
                        <label>Health Status</label>
                        <select name="child_health_status" id="healthStatus" onchange="toggleHealthDescription()"
                            required>
                            <option value="">-- Select Health Status --</option>
                            <option value="Healthy">Healthy</option>
                            <option value="Allergies">Allergies</option>
                            <option value="Disabilities">Disabilities</option>
                            <option value="Lifetime Diseases">Lifetime Diseases</option>
                        </select>
                    </div>

                    <div class="field" id="healthDescriptionField" style="display:none;">
                        <label>Please describe the disease or disability</label>
                        <textarea name="child_health_description" rows="4"
                            placeholder="Describe the disease or disability here..."></textarea>
                    </div>

                    <div class="field">
                        <label>Explain the situation that led you to seek orphanage care for this child</label>
                        <textarea name="child_story_behind" rows="4" required
                            placeholder="Share any important background information, challenges, or family circumstances..."></textarea>
                    </div>

                    <div class="field">
                        <label>Upload Birth Certificate (PDF only)</label>
                        <input type="file" name="child_pdf" id="child_pdf" accept="application/pdf" required>
                    </div>

                    <button name="submit_child" type="submit" class="ui primary button">Submit</button>
                    <button type="reset" class="ui button">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>

<script>
document.getElementById('showFormButton').addEventListener('click', function() {
    document.getElementById('childRegistrationForm').style.display = 'block';
    this.style.display = 'none';
});

function validateForm() {
    const fileInput = document.getElementById('child_pdf');
    const filePath = fileInput.value;
    const allowedExtension = /\.pdf$/i;
    if (!allowedExtension.exec(filePath)) {
        alert('Only PDF files are allowed for birth certificate upload.');
        fileInput.value = '';
        return false;
    }
    return true;
}

function toggleHealthDescription() {
    const healthStatus = document.getElementById('healthStatus').value;
    const healthDescriptionField = document.getElementById('healthDescriptionField');
    healthDescriptionField.style.display = (healthStatus === 'Disabilities' || healthStatus === 'Lifetime Diseases') ?
        'block' : 'none';
}
</script>
<!-- Left menu -->
<div class="four wide column" style="position: sticky; top: 1rem; align-self: flex-start; z-index: 10;">
    <div class="ui vertical menu fluid">
        <!-- Menu items with active state -->
        <a class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/index.php" ? "active" : "");?>" href="index.php">
            <i class="home icon"></i> Home
        </a>
        <a class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/child-gallery-sponsored.php" ? "active" : "");?>"
            href="child-gallery-sponsored.php">
            <i class="image icon"></i> Child Gallery
        </a>
        <a class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/resources.php" ? "active" : "");?>"
            href="resources.php">
            <i class="archive icon"></i> Resources Needed
        </a>
        <a class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/donation.php" ? "active" : "");?>"
            href="donation.php">
            <i class="heart icon"></i> Donation
        </a>
        <a class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/feedback-form.php" ? "active" : "");?>"
            href="feedback-form.php">
            <i class="comments icon"></i> Comments
        </a>
        <a class="item <?php echo ($_SERVER['PHP_SELF'] == "/orphan/contact-us.php" ? "active" : "");?>"
            href="contact-us.php">
            <i class="phone icon"></i> Contact Us
        </a>

        <!-- Brochure Download Section -->
        <div class="item">
            <div class="header">Download Brochure</div>
            <div class="description">Learn more about our center.</div>
            <a class="ui green fluid button" href="./downloads/GoodHopeHome_Brochure.pdf" download>
                <i class="download icon"></i> Download PDF
            </a>
        </div>
    </div>
</div>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include './components/db_connect.php';

if (
    !isset($_SESSION['user_id']) &&
    isset($_COOKIE['user_id']) &&
    isset($_COOKIE['username']) &&
    isset($_COOKIE['role'])
) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['role'] = $_COOKIE['role'];
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

include './components/header.php';
?>

<div class="ui container mt-4" style="font-size: 0.8rem;">
    <?php include './components/top-menu.php'; ?>

    <div class="ui grid stackable">
        <?php include './components/side-menu.php'; ?>

        <div class="twelve wide column">
            <div class="ui raised very padded segment" style="background: #f9f9f9;">
                <h2 class="ui teal header" style="font-size: 1.2rem;">
                    <i class="donate icon"></i>
                    <div class="content">
                        Donations
                        <div class="sub header" style="font-size: 0.9rem;">Support the children of GOOD HOPE HOME</div>
                    </div>
                </h2>

                <div class="ui relaxed divided list" style="padding-top: 1rem;">
                    <div class="item" style="font-size: 0.85rem;">
                        <i class="info icon blue large"></i>
                        <div class="content">
                            <p><strong>GOOD HOPE HOME</strong> in Arusha, Tanzania welcomes all generous donors and
                                sponsors to visit our center and explore the incredible work being done to support
                                vulnerable children.</p>
                        </div>
                    </div>

                    <div class="item" style="font-size: 0.85rem;">
                        <i class="check circle icon green large"></i>
                        <div class="content">
                            <p>We believe in <strong>transparency</strong>, <strong>care</strong>, and
                                <strong>impact</strong>. Every donation is used according to the center’s actual needs — nothing less, nothing more.</p>
                        </div>
                    </div>

                    <div class="item" style="font-size: 0.85rem;">
                        <i class="heart icon red large"></i>
                        <div class="content">
                            <p>Your contribution provides <strong>food</strong>, <strong>shelter</strong>,
                                <strong>education</strong>, and <strong>love</strong> to orphaned and underprivileged
                                children.</p>
                        </div>
                    </div>

                    <div class="item" style="font-size: 0.85rem;">
                        <i class="money bill alternate icon yellow large"></i>
                        <div class="content">
                            <p>Every Tanzanian Shilling (Tshs) counts. No amount is too small — it all helps build a
                                brighter future.</p>
                        </div>
                    </div>

                    <div class="item" style="font-size: 0.85rem;">
                        <i class="phone icon grey large"></i>
                        <div class="content">
                            <p>To learn more or schedule a visit, contact us at <strong>+255 766 575 400</strong> or
                                <strong>ellydecheever16@gmail.com</strong></p>
                        </div>
                    </div>
                </div>

                <div class="ui center aligned segment" style="margin-top: 2rem;">
                    <a class="ui small primary button" href="donation-form.php">
                        <i class="hand holding heart icon"></i>
                        Make a Contribution
                    </a>
                </div>

                <p class="ui center aligned grey text" style="font-style: italic; font-size: 0.8rem; margin-top: 1rem;">
                    Thank you for choosing to be part of this journey. Together, we restore hope and shape a brighter
                    tomorrow.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include './components/footer.php'; ?>

<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<!-- Side Navigation Bar (Menu) -->
<div class="four wide column" id="example1" style="margin-top: 1rem;">
    <div class="ui secondary vertical pointing menu" id="sideMenu"
        style="background-color: #ecf0f1; padding: 1rem; border-radius: 8px;">
        <a class="item <?= ($currentPage == "index.php") ? "active" : "" ?>" href="index.php">Dashboard</a>
        <a class="item <?= ($currentPage == "children.php") ? "active" : "" ?>" href="children.php">Children</a>
        <a class="item <?= ($currentPage == "sponsorer.php") ? "active" : "" ?>" href="sponsorer.php">Sponsors</a>
        <a class="item <?= ($currentPage == "donators.php") ? "active" : "" ?>" href="donators.php">Donors</a>
        <a class="item <?= ($currentPage == "gift-sent.php") ? "active" : "" ?>" href="gift-sent.php">Gift Sent</a>
        <a class="item <?= ($currentPage == "programs.php") ? "active" : "" ?>" href="programs.php">Resources</a>
        <a class="item <?= ($currentPage == "gallery.php") ? "active" : "" ?>" href="gallery.php">Gallery</a>
        <a class="item <?= ($currentPage == "feedback.php") ? "active" : "" ?>" href="feedback.php">User Comments</a>
        <a class="item <?= ($currentPage == "child-requests.php") ? "active" : "" ?>" href="child-requests.php">Child
            Requests</a>
            <a href="general-donation-balance.php" class="ui button primary">View General Donation Balance</a>

    </div>
</div>

<script>
// Get the side menu element
const sideMenu = document.getElementById('sideMenu');

// Initial position of the side menu
let lastScrollTop = 0;

// Listen for scroll events
window.addEventListener('scroll', function() {
    let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    // If scrolling up and the scroll position is greater than 100px from the top
    if (currentScroll < lastScrollTop && currentScroll > 100) {
        sideMenu.style.position = 'fixed';
        sideMenu.style.top = '0';
        sideMenu.style.zIndex = '10';
    } else {
        // Reset position when scrolling down or less than 100px from the top
        sideMenu.style.position = 'relative';
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
});
</script>
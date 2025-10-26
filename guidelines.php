<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="assets/css/style.css">

<section class="diagnose-bg">
    <h1 class="diagnose-title">User Guidelines</h1>
    <p><strong>To ensure accurate AI diagnosis, follow these image upload guidelines:</strong></p>


    <div class="guidelines-container">
        <!-- Left box: Checklist -->
        <div class="guideline-box">
        <p class="avoid-title"><strong>FOLLOW</strong></p>
        <ul class="diagnose-list checklist">
    <li>✔ Ensure a clear, focused image with good natural lighting and no filters or edits.</li>
    <li>✔ Only show the affected area; upload separate images for different conditions.</li>
    <li>✔ Use supported formats: JPG, PNG, or WEBP (Max Size: 5MB).</li>
</ul>

        </div>

        <!-- Right box: Avoid list -->
        <div class="guideline-box">
            <p class="avoid-title"><strong>AVOID</strong></p>
            <ul class="diagnose-list avoid-list">
                <li>✖ Low-resolution or dark images.</li>
                <li>✖ Makeup or skincare products covering the affected area.</li>
                <li>✖ Images with distractions (tattoos, jewelry, or clothing obstructing the skin).</li>
            </ul>
        </div>
    </div>

    <!-- Button Centered Below Both Boxes -->
    <div class="next-button-wrapper">
        <a href="diagnose.php" class="btn">NEXT</a>
    </div>
</section>

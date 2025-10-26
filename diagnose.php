<?php
session_start();
include 'includes/header.php';  // Include header
include 'includes/db.php';      // Include database connection

?>

<section class="diagnose-bg">
    <div class="diagnose-content">
        <h2 class="diagnose-title">Diagnose</h2>

        <?php if (!isset($_SESSION['username'])): ?>
            <ul class="diagnose-list">
                <li><strong>Get Instant AI-Powered Skin Analysis!</strong></li>
                <li><strong>Upload a skin image, enter your symptoms, and let AI analyze your condition in seconds!</strong></li>
                <li><strong>Detect & Track Skin Conditions Anytime, Anywhere.</strong></li>
                <li><strong>Receive accurate diagnosis, severity levels, and expert-backed recommendations—all at your fingertips.</strong></li>
                <li><strong>Sign in or create an account to access your personalized skin health insights and track your progress over time!</strong></li>
            </ul>
        <?php else: ?>
            <div class="diagnosis-container">
            <form action="analyze.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="image" id="image" required>
    <button type="submit" class="btn">Get Diagnosis</button>
</form>

            

                
            </div>
        <?php endif; ?>
    </div>
</section>

</body>
</html>

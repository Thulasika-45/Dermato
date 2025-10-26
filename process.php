<?php include 'includes/header.php'; ?>

<section class="reset-password-bg">
    <div class="reset-password-content">
        <h2 class="reset-password-title">RESET PASSWORD</h2>
        <form class="reset-password-form" method="POST" action="process_reset.php">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Enter your new password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Confirm your new password" required>

            <button type="submit">Reset Password</button>

            <?php if (isset($_SESSION['error'])) { echo "<p class='error-message'>{$_SESSION['error']}</p>"; unset($_SESSION['error']); } ?>
        </form>
    </div>
</section>

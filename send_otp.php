<?php include 'includes/header.php'; ?>

<section class="send-otp-bg">
    <div class="send-otp-content">
        <h2 class="send-otp-title">VERIFY OTP</h2>
        <form class="send-otp-form" method="POST" action="verify_otp.php">
            <label>Enter OTP</label>
            <input type="text" name="otp" placeholder="Enter the OTP sent to your email" required>

            <button type="submit">Verify OTP</button>

            <a href="forgot_password.php" class="resend-otp">Resend OTP</a>
        </form>
    </div>
</section>

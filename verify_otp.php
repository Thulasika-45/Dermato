<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST["otp"]);
    $user_id = $_SESSION['reset_user_id'];

    $stmt = $conn->prepare("SELECT otp_code FROM otp_verification WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($stored_otp);
    $stmt->fetch();

    if ($entered_otp == $stored_otp) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid OTP!";
    }
}
?>

<?php include 'includes/header.php'; ?>
<section class="login-bg">
    <div class="container">
        <h2>Verify OTP</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <label>Enter OTP</label>
            <input type="text" name="otp" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</section>

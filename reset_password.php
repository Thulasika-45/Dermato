<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['otp_verified'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $user_id = $_SESSION['reset_user_id'];

        // Update password (add hashing if needed)
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_password, $user_id);
        $stmt->execute();

        // Clear session
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['otp_verified']);

        echo "<script>alert('Password reset successful!'); window.location.href='login.php';</script>";
        exit();
    }
}
?>

<?php include 'includes/header.php'; ?>
<section class="login-bg">
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <label>New Password</label>
            <input type="password" name="password" required>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
            <button type="submit">Change Password</button>
        </form>
    </div>
</section>

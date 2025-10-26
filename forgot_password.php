<?php
session_start();
include 'includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);

    $stmt = $conn->prepare("SELECT id, email FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $email);
        $stmt->fetch();

        $otp = rand(100000, 999999);

        $insert = $conn->prepare("INSERT INTO otp_verification (user_id, otp_code, created_at) VALUES (?, ?, NOW())");
        $insert->bind_param("is", $user_id, $otp);
        $insert->execute();

        // Send OTP using Gmail SMTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'thulasikaalaganan45@gmail.com'; // your gmail
            $mail->Password   = 'zuwv nfoi wauc tjyz';             // use app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('thulasikaalaganan45@gmail.com', 'Dermato');
            $mail->addAddress($email, $username);

            $mail->isHTML(true);
            $mail->Subject = 'Dermato - Password Reset OTP';
            $mail->Body    = "Hi <strong>$username</strong>,<br><br>Your OTP is: <strong>$otp</strong><br><br>Please do not share this with anyone.<br><br>Regards,<br>Dermato Team";

            $mail->send();
            $_SESSION['reset_user_id'] = $user_id;
            header("Location: verify_otp.php");
            exit();

        } catch (Exception $e) {
            $error = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css">

<section class="forgot-password-bg">
    <div class="forgot-password-container">
        <h2 class="forgot-password-title">Forgot Password?</h2>

        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <form class="forgot-password-form" method="POST">
            <label>Enter your username</label>
            <input type="text" name="username" required>
            <button type="submit">Send OTP</button>
            <a href="login.php" class="back-to-login">Back to Login</a>
        </form>
    </div>
</section>

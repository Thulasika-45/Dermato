<?php 
session_start();
include 'includes/db.php'; 
$error = ""; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required!";
    } else {
        // Check user in the database
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_password);
            $stmt->fetch();
            
            // Check password (remove this if passwords are stored in plaintext)
            if ($password === $db_password) { // If you use hashed passwords, replace with password_verify($password, $db_password)
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $username;
                echo "<script>alert('Login successful!'); window.location.href='guidelines.php';</script>";
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }
        
        $stmt->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<section class="login-bg">
    <div class="container">
        <h2 class="login-title">Log In</h2>
        <div class="login-content">
            <?php if(!empty($error)) { echo "<p class='error' style='color: red;'>$error</p>"; } ?>

            <form class="login-form" method="POST" action="">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                <button type="submit" style="background: #533829;">LOG IN</button>
            </form>
        </div>
        <a href="forgot_password.php">Forgot Password?</a>
    </div>
</section>

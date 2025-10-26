<?php
session_start();
include 'includes/db.php'; // Ensure this file correctly connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: signup.php"); // Redirect back to form
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: signup.php");
        exit();
    }

    // Check if email or username already exists
    $checkUser = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        $_SESSION['error'] = "Username or Email already exists!";
        header("Location: signup.php");
        exit();
    }
    $checkUser->close();

    // Hash the password
    $plainPassword = $password;

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $username, $email, $plainPassword);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Account created successfully! Please login.";
        header("Location: login.php"); // Redirect to login page
        exit();
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: signup.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>


<?php include 'includes/header.php'; ?> 
<section class="signup-bg"> 
<div class="signup-content"> 
<h2 class="signup-title">Sign Up</h2> 
<form class="signup-form" method="POST" action=""> 
<label>Username</label> 
<input type="text" name="username" placeholder="Enter your username" required> 
<label>Email</label> 
<input type="email" name="email" placeholder="Enter your email" required> 
<label>Password</label> 
<input type="password" name="password" placeholder="Enter your password" required> 
<label>Confirm Password</label> 
<input type="password" name="confirm_password" placeholder="Confirm your password" required> 
<button type="submit">SUBMIT</button> 
</form> 
</div> 
</section> 

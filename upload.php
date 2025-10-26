<?php
session_start();
include 'includes/db.php';  // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $target_dir = "uploads/";  // Directory to save images
    $image_name = time() . "_" . basename($_FILES["image"]["name"]);  // Unique filename
    $target_file = $target_dir . $image_name;

    // Check file type (only allow jpg, jpeg, png)
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (!in_array($imageFileType, $allowed_types)) {
        die("Only JPG, JPEG, and PNG files are allowed.");
    }

    // Move uploaded file to "uploads/" directory
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $user_id = $_SESSION['user_id'];  // Get logged-in user ID

        // Store file path in the database
        $stmt = $conn->prepare("INSERT INTO uploads (user_id, file_path) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $target_file);
        $stmt->execute();
        $stmt->close();

        // Redirect to diagnose.php with image preview
        header("Location: diagnose.php?image=" . urlencode($target_file));
        exit();
    } else {
        die("File upload failed.");
    }
} else {
    die("Invalid request.");
}
?>

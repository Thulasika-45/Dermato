<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT username, email, gender, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST['new_name'];
    $new_gender = $_POST['new_gender'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Profile Picture Upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE username = ?");
        $stmt->bind_param("ss", $target_file, $username);
        $stmt->execute();
        $user['profile_picture'] = $target_file;
    }

    // Update name and gender
    $stmt = $conn->prepare("UPDATE users SET username = ?, gender = ? WHERE username = ?");
    $stmt->bind_param("sss", $new_name, $new_gender, $username);
    $stmt->execute();

    if ($username !== $new_name) {
        $_SESSION['username'] = $new_name;
        $username = $new_name;
    }

    $user['username'] = $new_name;
    $user['gender'] = $new_gender;

    // Password update
    if (!empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $new_password, $username);
            $stmt->execute();
            $success = "Profile and password updated successfully!";
        } else {
            $error = "Passwords do not match!";
        }
    } else {
        $success = "Profile updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Dermato</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .rounded-profile {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #533829;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
    <div class="profile-bg">
        <div class="profile-container">
            <h2 class="profile-title">👤 User Profile</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="profile-info text-center">
                    <label>Profile Picture:</label><br>
                    <img src="<?php echo $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'assets/images/default-profile.jpg'; ?>" alt="Profile Picture" class="rounded-profile mb-2">
                    <input type="file" name="profile_picture" class="form-control mt-2">
                </div>

                <div class="profile-info">
                    <label>Name:</label>
                    <input type="text" name="new_name" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="profile-info">
                    <label>Email:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>

                <div class="profile-info">
                    <label>Gender:</label>
                    <select name="new_gender" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Male" <?php if($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>

                <hr>
                <h4 class="text-center mb-3">🔐 Change Password</h4>
                <div class="profile-info">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="profile-info">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>

                <div class="profile-actions mt-3 text-center">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting (Remove this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>  <!-- Start session at the beginning -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dermato - AI Skin Diagnosis</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- ✅ Ensure correct path -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <!-- Background Overlay -->
    <div class="background-overlay">
        <header class="bg">
            <div class="container py-2">
                <nav class="navbar navbar-expand-lg">
                    <a class="navbar-brand" href="index.php">
                        <img src="assets/images/logo.jpg" alt="Dermato Logo" class="img-fluid" style="width: auto;height:90px;">
                    </a>
                    <button class="navbar-toggler dmy" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a href="index.php" class="nav-link text-dark">HOME</a></li>
                            <li class="nav-item"><a href="about.php" class="nav-link text-dark">ABOUT US</a></li>
                            <li class="nav-item"><a href="diagnose.php" class="nav-link text-dark">DIAGNOSE</a></li>

                            <?php if (isset($_SESSION['username'])): ?>  
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo strtoupper(htmlspecialchars($_SESSION['username'])); ?>
        </a>
        <ul class="dropdown-menu" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
    </li>
<?php else: ?>
    <li class="nav-item"><a href="signup.php" class="nav-link text-dark">SIGN UP</a></li>
    <li class="nav-item"><a href="login.php" class="nav-link text-dark">LOG IN</a></li>
<?php endif; ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
    </div>
</body>
</html>
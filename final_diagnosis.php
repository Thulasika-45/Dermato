<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view the diagnosis result.";
    exit();
}

if (!isset($_GET['upload_id'])) {
    echo "Invalid access. Upload ID not found.";
    exit();
}

$upload_id = intval($_GET['upload_id']);
$user_id = $_SESSION['user_id'];

// Fetch diagnosis and file details from DB
$stmt = $conn->prepare("SELECT dr.disease, dr.description, dr.recommendation, u.file_path 
                        FROM diagnosis_results dr 
                        JOIN uploads u ON dr.upload_id = u.id 
                        WHERE dr.upload_id = ? AND dr.user_id = ?");
$stmt->bind_param("ii", $upload_id, $user_id);
$stmt->execute();
$stmt->bind_result($disease, $description, $recommendation, $file_path);
?>

<!-- ✅ Include Header and CSS -->
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style.css"> <!-- Make sure this file contains .diagnosis-bg and .diagnosis-card classes -->

<!-- ✅ Begin Page Content -->
<div class="diagnosis-bg">
    <?php if ($stmt->fetch()): ?>
        <div class="diagnosis-card">
            <h2 class="diagnose-title">DIAGNOSIS REPORT</h2>
            <img src="<?php echo htmlspecialchars($file_path); ?>" alt="Uploaded Image" class="diagnosis-image">
            <p><strong>Predicted Disease:</strong> <?php echo htmlspecialchars($disease); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($description); ?></p>
            <p><strong>Recommendation:</strong> <?php echo htmlspecialchars($recommendation); ?></p>
            <a href="download_pdf.php?upload_id=<?php echo $upload_id; ?>" class="btn btn-success mt-3" target="_blank">📥 Download Report</a>

        </div>
    <?php else: ?>
        <div class="diagnosis-card">
            <p>No results found.</p>
        </div>
    <?php endif; ?>
</div>

<?php $stmt->close(); ?>
</body>
</html>

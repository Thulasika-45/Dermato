<?php
require 'vendor/autoload.php'; // Autoload dompdf classes
use Dompdf\Dompdf;

session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['upload_id'])) {
    echo "Unauthorized access.";
    exit();
}

$user_id = $_SESSION['user_id'];
$upload_id = intval($_GET['upload_id']);

// Fetch user info
$stmtUser = $conn->prepare("SELECT username, email, gender FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$stmtUser->bind_result($username, $email, $gender);
$stmtUser->fetch();
$stmtUser->close();

// Fetch diagnosis info
$stmt = $conn->prepare("SELECT dr.disease, dr.description, dr.recommendation, u.file_path 
                        FROM diagnosis_results dr 
                        JOIN uploads u ON dr.upload_id = u.id 
                        WHERE dr.upload_id = ? AND dr.user_id = ?");
$stmt->bind_param("ii", $upload_id, $user_id);
$stmt->execute();
$stmt->bind_result($disease, $description, $recommendation, $file_path);
$stmt->fetch();
$stmt->close();

// Base64 encode images
$logoPath = 'assets/images/logo.jpg'; // Adjust path
$logoBase64 = base64_encode(file_get_contents($logoPath));
$userImageBase64 = base64_encode(file_get_contents($file_path));

// Build HTML for PDF
$html = "
    <div style='font-family: Arial, sans-serif; padding: 30px; border: 3px solid #533829;'>
        <div style='text-align:center; margin-bottom: 20px;'>
            <img src='data:image/jpeg;base64,{$logoBase64}' style='width:80px;'>
            <h2 style='color: #533829; margin-top: 10px;'>Dermato - Diagnosis Report</h2>
        </div>

        <h3 style='margin-top: 20px; color: #333;'>Patient Info</h3>
        <p><strong>Username:</strong> " . htmlspecialchars($username) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Gender:</strong> " . htmlspecialchars($gender) . "</p>

        <hr style='margin: 20px 0;'>

        <h3 style='color: #333;'>Diagnosis Details</h3>
        <p><strong>Predicted Disease:</strong> " . htmlspecialchars($disease) . "</p>
        <p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($description)) . "</p>
        <p><strong>Recommendation:</strong><br>" . nl2br(htmlspecialchars($recommendation)) . "</p>

        <div style='text-align:center; margin-top:30px;'>
            <h4 style='margin-bottom: 10px;'>Uploaded Image</h4>
            <img src='data:image/jpeg;base64,{$userImageBase64}' style='max-width:300px; border:1px solid #ccc; padding:5px; border-radius:8px;'>
        </div>
    </div>
";


// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Stream PDF
$dompdf->stream("Dermato_Diagnosis_Report.pdf", ["Attachment" => true]);
exit;
?>

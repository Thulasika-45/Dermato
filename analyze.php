<?php
session_start();
include 'includes/db.php'; // Make sure this sets $conn

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to submit a diagnosis.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $user_id = $_SESSION['user_id'];
    $image = $_FILES["image"];
    $imagePath = $image["tmp_name"];
    $imageName = basename($image["name"]);
    $target_file = "uploads/" . uniqid() . "_" . $imageName;

    // Move the uploaded file to the server
    if (!move_uploaded_file($image["tmp_name"], $target_file)) {
        echo "Error uploading image.";
        exit();
    }

    // Insert into uploads table
    $stmt1 = $conn->prepare("INSERT INTO uploads (user_id, file_path, upload_time) VALUES (?, ?, NOW())");
    $stmt1->bind_param("is", $user_id, $target_file);
    $stmt1->execute();
    $upload_id = $stmt1->insert_id;
    $stmt1->close();

    // Call Flask API
    $api_url = "http://127.0.0.1:5000/diagnose";
    $curl = curl_init();
    $cfile = new CURLFile($target_file, $image['type'], $image['name']);

    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array('file' => $cfile),
        CURLOPT_HTTPHEADER => array("Content-Type: multipart/form-data")
    ));

    $response = curl_exec($curl);
    if ($response === false) {
        echo "Curl Error: " . curl_error($curl);
        curl_close($curl);
        exit();
    }
    curl_close($curl);

    $result = json_decode($response, true);
    if (!$result || !isset($result['disease'])) {
        echo "<h3>Error: Could not get valid response from AI model.</h3>";
        echo "<pre>Response: $response</pre>";
        exit();
    }

    $disease = $result['disease'];
    $description = $result['description'] ?? 'N/A';
    $recommendation = $result['recommendation'] ?? 'N/A';

   // Insert into diagnosis_results
$stmt2 = $conn->prepare("INSERT INTO diagnosis_results (user_id, upload_id, disease, description, recommendation, diagnosed_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt2->bind_param("iisss", $user_id, $upload_id, $disease, $description, $recommendation);
$stmt2->execute();
$stmt2->close();

// ✅ Set flag to prevent showing image again in diagnose.php
$_SESSION['diagnosis_done'] = true;

// Redirect to final_diagnose.php with upload_id
header("Location: final_diagnosis.php?upload_id=$upload_id");
exit();


} else {
    echo "Invalid request. Please upload an image.";
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db.php';

// Check for AJAX request and proper content type
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Validate session and required parameters
if (!isset($_SESSION['user_id']) || !isset($_POST['anime_id'])) {
    http_response_code(400);
    exit('Invalid request');
}

$user_id = $_SESSION['user_id'];

// Sanitize and validate inputs
$anime_id = isset($_POST['anime_id']) ? trim($_POST['anime_id']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$ep = isset($_POST['episode']) ? intval($_POST['episode']) : 1;

// Validate inputs
if (empty($anime_id) || empty($title) || $ep < 1) {
    http_response_code(400);
    exit('Invalid input data');
}

// Limit title length to prevent abuse
if (strlen($title) > 255) {
    $title = substr($title, 0, 255);
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO watch_history (user_id, anime_id, anime_title, episode) 
        VALUES (?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE episode = ?, watched_at = CURRENT_TIMESTAMP");

if ($stmt) {
    $stmt->bind_param("issii", $user_id, $anime_id, $title, $ep, $ep);
    $stmt->execute();
    $stmt->close();
}

// Return success response
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
?>
<?php
/**
 * Configuration Example for Zentrix Anime Portal
 * 
 * Copy this file to _config.php and fill in your actual credentials.
 * Never commit _config.php with real credentials to version control!
 */

// Security settings
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

// Session start with check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Database Connection
$dbHost = 'localhost';           // Your MySQL hostname
$dbUser = 'your_username';       // Your database username
$dbPass = 'your_password';       // Your database password
$dbName = 'zentrix_anime';       // Your database name

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed. Please try again later.");
}

// Set charset
$conn->set_charset("utf8mb4");

// 2. Global Variables
$websiteTitle = "Zentrix Stream";
$websiteUrl = "https://yourdomain.com";  // Change to your domain
$version = "2.0.0";

// 3. Security Helper Functions

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function sanitizeInt($value, $default = 0) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int)$value : $default;
}

// 4. AniList GraphQL Fetcher Function
function fetchAniList($query, $variables = []) {
    $url = 'https://graphql.anilist.co';
    $data = json_encode(['query' => $query, 'variables' => $variables]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || $result === false) {
        error_log("AniList API error: HTTP $httpCode");
        return ['error' => 'Failed to fetch data from AniList'];
    }
    
    $decoded = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("AniList JSON decode error: " . json_last_error_msg());
        return ['error' => 'Invalid response from AniList'];
    }
    
    return $decoded;
}
?>

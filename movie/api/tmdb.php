<?php
/**
 * TMDB API Proxy Endpoint
 * Securely handles TMDB API requests without exposing API keys
 * 
 * This file acts as a server-side proxy to TMDB API to prevent
 * API key exposure in client-side JavaScript.
 */

require_once __DIR__ . '/../_config.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get endpoint and parameters
$endpoint = $_GET['endpoint'] ?? '';
$allowedEndpoints = [
    '/trending/all/day',
    '/trending/all/week',
    '/movie/now_playing',
    '/movie/popular',
    '/movie/upcoming',
    '/tv/on_the_air',
    '/tv/popular',
    '/tv/top_rated',
    '/search/movie',
    '/search/tv',
    '/search/multi',
    '/movie/',
    '/tv/',
    '/discover/movie',
    '/discover/tv'
];

// Validate endpoint
$isValid = false;
foreach ($allowedEndpoints as $allowed) {
    if (strpos($endpoint, $allowed) === 0) {
        $isValid = true;
        break;
    }
}

if (!$isValid) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid endpoint']);
    exit;
}

// Build parameters array from query string (excluding endpoint)
$params = $_GET;
unset($params['endpoint']);

// Fetch data from TMDB
$data = fetchTMDB($endpoint, $params);

// Return JSON response
header('Content-Type: application/json');
header('Cache-Control: public, max-age=300'); // Cache for 5 minutes

// Handle errors
if (isset($data['error'])) {
    http_response_code(500);
}

echo json_encode($data);
?>

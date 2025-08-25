<?php
// File Path: portfolio-api/config/cors.php

// This file handles all CORS and header requirements for the API.

// Allow requests from your specific frontend origin.
header("Access-Control-Allow-Origin: https://portfoliofront-h9zn.vercel.app/");
// --- THIS IS THE FIX ---
// Allow the browser to send and receive cookies for session management.
header("Access-Control-Allow-Credentials: true");
// Set the content type for all responses to JSON.
header("Content-Type: application/json; charset=UTF-8");
// Specify which HTTP methods are allowed.
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Specify which headers can be used during the actual request.
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// The maximum time (in seconds) the results of a preflight request can be cached.
header("Access-Control-Max-Age: 3600");

// The browser sends an OPTIONS request first to check permissions (preflight).
// We must respond with a 200 OK status to this request and then stop the script.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    // No further processing is needed for an OPTIONS request.
    exit(0);
}
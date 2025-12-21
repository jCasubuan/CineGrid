<?php
// Start session once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once __DIR__ . '/db_connect.php';

// Current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');
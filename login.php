<?php
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    $_SESSION['login_error'] = 'Email and password are required.';
    header('Location: index.php');
    exit;
}

// TEMPORARY LOGIN LOGIC
if ($email === 'test@cinegrid.com' && $password === 'password123') {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'Test User';
    $_SESSION['user_email'] = $email;

    header('Location: index.php');
    exit;
}

// Failed login
$_SESSION['login_error'] = 'Invalid email or password.';
header('Location: index.php');
exit;
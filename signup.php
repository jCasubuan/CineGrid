<?php
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    header('Location: index.php?error=emptyfields');
    exit;
}

/*
 TEMPORARY SIGNUP LOGIC
 Replace later with INSERT INTO users table
*/
$_SESSION['user_id'] = 2;
$_SESSION['user_name'] = $name;
$_SESSION['user_email'] = $email;

header('Location: profile.php');
exit;
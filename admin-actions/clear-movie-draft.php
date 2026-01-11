<?php
require_once '../includes/init.php';

unset($_SESSION['movie_draft']);

header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
exit; 
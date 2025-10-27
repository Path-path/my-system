<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

// Fetch count of unseen notifications
$result = $conn->query("SELECT COUNT(*) AS count FROM notifications WHERE viewed=0");
$row = $result->fetch_assoc();

echo json_encode($row);
?>

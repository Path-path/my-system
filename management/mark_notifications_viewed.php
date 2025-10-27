<?php
session_start();
include 'db.php';
$conn->query("UPDATE notifications SET viewed=1 WHERE viewed=0");
echo json_encode(['success'=>true]);

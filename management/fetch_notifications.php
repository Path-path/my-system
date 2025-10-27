<?php
session_start();
include 'db.php';

// Fetch unread notifications
$notifications = $conn->query("
    SELECT n.id, o.id AS order_id, c.fullname 
    FROM notifications n 
    JOIN orders o ON n.order_id = o.id 
    JOIN customers c ON o.customer_id = c.id 
    WHERE n.viewed = 0 
    ORDER BY n.created_at DESC
");

// Output only HTML
while($notif = $notifications->fetch_assoc()) {
    echo '<div class="notification" onclick="markViewed('.$notif['id'].', this)">';
    echo 'New order #'.$notif['order_id'].' from '.htmlspecialchars($notif['fullname']);
    echo '</div>';
}
?>

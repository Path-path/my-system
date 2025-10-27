<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];


function logStatusChange($conn, $order_id, $old_status, $new_status, $changed_by) {
    $stmt = $conn->prepare("INSERT INTO order_status_history (order_id, old_status, new_status, changed_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $order_id, $old_status, $new_status, $changed_by);
    $stmt->execute();
    $stmt->close();
}


if (isset($_POST['ship_order'])) {
    $order_id = intval($_POST['order_id']);
    $stmt = $conn->prepare("UPDATE orders SET status='Shipped' WHERE id=? AND status='Pending'");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        logStatusChange($conn, $order_id, 'Pending', 'Shipped', 'admin');
    }
    $stmt->close();

    $stmt2 = $conn->prepare("INSERT INTO notifications (order_id) VALUES (?)");
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();
    $stmt2->close();
}

if (isset($_POST['to_receive_order'])) {
    $order_id = intval($_POST['order_id']);
    $stmt = $conn->prepare("UPDATE orders SET status='To Receive' WHERE id=? AND status='Shipped'");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        logStatusChange($conn, $order_id, 'Shipped', 'To Receive', 'admin');
    }
    $stmt->close();

    $stmt2 = $conn->prepare("INSERT INTO notifications (order_id) VALUES (?)");
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();
    $stmt2->close();
}


if (isset($_POST['view_notification_id'])) {
    $notif_id = intval($_POST['view_notification_id']);
    $stmt = $conn->prepare("UPDATE notifications SET viewed=1 WHERE id=?");
    $stmt->bind_param("i", $notif_id);
    $stmt->execute();
    $stmt->close();
    exit;
}

$orders = $conn->query("
    SELECT 
        ROW_NUMBER() OVER (ORDER BY o.created_at ASC) AS display_id,
        o.id AS real_order_id,
        o.customer_id, 
        c.fullname AS customer_name, 
        o.total, o.status, o.created_at
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    ORDER BY o.created_at DESC
");


$notifications = $conn->query("
    SELECT n.id, o.id AS order_id, c.fullname 
    FROM notifications n 
    JOIN orders o ON n.order_id = o.id 
    JOIN customers c ON o.customer_id = c.id 
    WHERE n.viewed = 0 
    ORDER BY n.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>ShopSphere - Orders</title>
   <style>
   
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

body {
    background: linear-gradient(135deg, #FFF8E1, #FFF3CD);
    color: #333;
}

/* ====== Sidebar ====== */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100%;
    background: linear-gradient(180deg, #FFF9C4, #FFEB99);
    color: #333;
    padding: 20px;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar h2 {
    font-size: 1.6rem;
    margin-bottom: 10px;
    color: #6B4E00;
}

.sidebar .welcome {
    font-size: 0.95rem;
    margin-bottom: 25px;
    font-style: italic;
    color: #8B6F00;
}

.sidebar ul {
    list-style: none;
    flex-grow: 1;
}

.sidebar ul li {
    margin: 12px 0;
}

.sidebar ul li a {
    text-decoration: none;
    color: #333;
    padding: 10px 15px;
    display: block;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar ul li a:hover {
    background: rgba(255, 255, 255, 0.5);
}

.sidebar ul li a.logout {
    background: #F87171;
    font-weight: 600;
    text-align: center;
    color: #fff;
}

.sidebar ul li a.logout:hover {
    background: #DC2626;
}

/* ====== Main Content ====== */
.main-content {
    margin-left: 250px;
    padding: 30px;
}

.main-content h1 {
    color: #D97706;
    margin-bottom: 20px;
    font-size: 2rem;
}

/* ====== Orders Table ====== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

th {
    background: #FACC15;
    color: #6B4E00;
    font-weight: 600;
}

tr:nth-child(even) {
    background: #FFFDF2;
}

/* ====== Buttons ====== */
.btn-ship, .btn-to-receive {
    padding: 8px 14px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
    color: #fff;
}

.btn-ship {
    background: #F59E0B;
}

.btn-ship:hover {
    background: #D97706;
}

.btn-to-receive {
    background: #FBBF24;
    color: #333;
}

.btn-to-receive:hover {
    background: #F59E0B;
    color: #fff;
}

/* ====== Status Colors ====== */
.status-pending {
    color: #F97316;
    font-weight: bold;
}

.status-shipped {
    color: #2563EB;
    font-weight: bold;
}

.status-to-receive {
    color: #6D28D9;
    font-weight: bold;
}

.status-received {
    color: #16A34A;
    font-weight: bold;
}

/* ====== Notifications ====== */
.notification {
    background: #FFFBEA;
    border-left: 5px solid #FACC15;
    padding: 10px 15px;
    margin-bottom: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.notification:hover {
    background: #FFF3CD;
}

/* ====== Responsive ====== */
@media (max-width: 768px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .sidebar ul {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .main-content {
        margin-left: 0;
        margin-top: 20px;
        padding: 20px;
    }
    th, td {
        font-size: 0.85rem;
        padding: 10px;
    }
}

</style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function markViewed(id, el){
            $.post('orders.php',{view_notification_id:id},function(){ $(el).fadeOut(); });
        }
        function loadNotifications(){
            $.get('fetch_notifications.php', function(data){ $('#notifications-container').html(data); });
        }
        setInterval(loadNotifications, 5000);
        $(document).ready(function(){ loadNotifications(); });
    </script>
</head>
<body>
<div class="sidebar">
    <h2>ShopSphere</h2>
    <p class="welcome">Welcome, <?= ucfirst($username); ?> (<?= $role; ?>)</p>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="products.php">📦 Products</a></li>
        <li><a href="sales.php">💰 Sales</a></li>
        <li><a href="customers.php">👥 Customers</a></li>
        <li><a href="inventory.php">📊 Inventory</a></li>
        <li><a href="reports.php">📑 Reports</a></li>
        <li><a href="orders.php">🛒 Orders</a></li>
        <li><a href="logout.php" class="logout">🚪 Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Orders</h1>

    <div id="notifications-container">
        <?php while($notif = $notifications->fetch_assoc()): ?>
            <div class="notification" onclick="markViewed(<?= $notif['id']; ?>, this)">
                New order #<?= $notif['order_id']; ?> from <?= htmlspecialchars($notif['fullname']); ?>
            </div>
        <?php endwhile; ?>
    </div>

    <table>
        <tr>
            <th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th>
        </tr>
        <?php while($row = $orders->fetch_assoc()): ?>
        <tr>
            
            <td>#<?= $row['display_id']; ?></td>
            <td><?= htmlspecialchars($row['customer_name']); ?></td>
            <td>₱<?= number_format($row['total'],2); ?></td>
            <td class="<?= 
                $row['status']=='Pending' ? 'status-pending' : 
                ($row['status']=='Shipped' ? 'status-shipped' : 
                ($row['status']=='To Receive' ? 'status-to-receive' : 'status-received')); ?>">
                <?= $row['status']; ?>
            </td>
            <td><?= $row['created_at']; ?></td>
            <td>
                <?php if($row['status']=='Pending'): ?>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="order_id" value="<?= $row['real_order_id']; ?>">
                        <button type="submit" name="ship_order" class="btn-ship">Mark as Shipped</button>
                    </form>
                <?php elseif($row['status']=='Shipped'): ?>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="order_id" value="<?= $row['real_order_id']; ?>">
                        <button type="submit" name="to_receive_order" class="btn-to-receive">Mark as To Receive</button>
                    </form>
                <?php elseif($row['status']=='To Receive'): ?>
                    Waiting for customer confirmation
                <?php elseif($row['status']=='Received'): ?>
                    ✅ Customer confirmed
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

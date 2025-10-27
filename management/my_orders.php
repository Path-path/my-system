<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];


function logStatusChange($conn, $order_id, $old_status, $new_status, $changed_by) {
    $stmt = $conn->prepare("INSERT INTO order_status_history (order_id, old_status, new_status, changed_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $order_id, $old_status, $new_status, $changed_by);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['receive_order_id'])) {
    $order_id = intval($_POST['receive_order_id']);
    $stmt = $conn->prepare("UPDATE orders SET status='Received' WHERE id=? AND customer_id=? AND status='To Receive'");
    $stmt->bind_param("ii", $order_id, $customer_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        logStatusChange($conn, $order_id, 'To Receive', 'Received', 'customer');
    }
    $stmt->close();
    header("Location: my_orders.php");
    exit;
}

$orders = $conn->query("
    SELECT o.id AS order_id, o.total, o.status, o.created_at, 
           p.name AS product_name, oi.quantity, (oi.quantity * p.price) AS subtotal
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.customer_id = '$customer_id'
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <style>
       
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Roboto, sans-serif;
}

body {
    background: linear-gradient(135deg, #FFF8E1, #FFF3CD);
    padding: 20px;
    color: #333;
}

/* ====== Heading ====== */
h2 {
    color: #D97706;
    margin-bottom: 25px;
    text-align: center;
    font-size: 1.8rem;
}

/* ====== Back Button ====== */
.back-btn {
    display: inline-block;
    padding: 10px 18px;
    background: #F59E0B;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: bold;
    transition: background 0.3s;
}

.back-btn:hover {
    background: #D97706;
}

/* ====== Table ====== */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-top: 15px;
}

th, td {
    padding: 14px;
    border-bottom: 1px solid #eee;
    text-align: center;
}

th {
    background: #FACC15;
    color: #6B4E00;
    text-transform: uppercase;
    font-size: 0.95rem;
    font-weight: 600;
}

tr:nth-child(even) {
    background: #FFFDF2;
}

tr:hover {
    background: #FFF9C4;
}

/* ====== Status Badges ====== */
.status-shipped {
    color: #2563EB;
    font-weight: bold;
}

.status-to-receive {
    color: #9333EA;
    font-weight: bold;
}

.status-received {
    color: #16A34A;
    font-weight: bold;
}

/* ====== Confirm Button ====== */
.btn-receive {
    padding: 8px 14px;
    background: #10B981;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s;
}

.btn-receive:hover {
    background: #059669;
}

/* ====== Responsive ====== */
@media(max-width: 768px) {
    table, thead, tbody, th, td, tr {
        font-size: 0.85rem;
    }

    .back-btn {
        display: block;
        width: 100%;
        text-align: center;
        margin-bottom: 20px;
    }
}

    </style>
</head>
<body>
    <h2>📦 My Orders</h2>
    <a href="customer_dashboard.php" class="back-btn">⬅ Back</a>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php 
        $counter = 1; // ✅ Sequential counter
        while($row = $orders->fetch_assoc()): ?>
        <tr>
            <td>#<?= $counter++; ?></td> <!-- Sequential instead of DB id -->
            <td><?= $row['product_name']; ?></td>
            <td><?= $row['quantity']; ?></td>
            <td>₱<?= number_format($row['subtotal'], 2); ?></td>
            <td>₱<?= number_format($row['total'], 2); ?></td>
            <td class="<?= 
                $row['status']=='Shipped' ? 'status-shipped' : 
                ($row['status']=='To Receive' ? 'status-to-receive' : 
                ($row['status']=='Received' ? 'status-received' : '')); ?>">
                <?= $row['status']; ?>
            </td>
            <td><?= $row['created_at']; ?></td>
            <td>
                <?php if($row['status'] == 'To Receive'): ?>
                    <form method="post">
                        <input type="hidden" name="receive_order_id" value="<?= $row['order_id']; ?>">
                        <button type="submit" class="btn-receive">Confirm Received</button>
                    </form>
                <?php elseif($row['status'] == 'Received'): ?>
                    ✅ Received
                <?php elseif($row['status'] == 'Shipped'): ?>
                    Waiting for "To Receive"
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Check selected products
if (!isset($_POST['selected']) || count($_POST['selected']) == 0) {
    echo "<script>alert('⚠️ Please select at least one product to checkout.'); window.location.href='cart.php';</script>";
    exit;
}

$selected = $_POST['selected'];
$total = 0;


$ids = implode(",", array_map('intval', $selected));
$result = $conn->query("
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id=p.id
    WHERE c.customer_id='$customer_id' AND c.id IN ($ids)
");

$products = [];
while ($row = $result->fetch_assoc()) {
    $line_total = $row['price'] * $row['quantity'];
    $total += $line_total;
    $products[] = $row;
}

// ✅ Insert order first
$stmt = $conn->prepare("INSERT INTO orders (customer_id, total, status, created_at) VALUES (?, ?, 'Pending', NOW())");
$stmt->bind_param("id", $customer_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();


foreach ($products as $p) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $p['product_id'], $p['quantity'], $p['price']);
    $stmt->execute();
    $stmt->close();

    $delete = $conn->prepare("DELETE FROM cart WHERE id=?");
    $delete->bind_param("i", $p['cart_id']);
    $delete->execute();
    $delete->close();
}


$message = "New order #$order_id placed.";
$stmt = $conn->prepare("INSERT INTO notifications (order_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $order_id, $message);
$stmt->execute();
$stmt->close();

echo "<script>alert('✅ Checkout successful! Your order #$order_id has been placed.'); window.location.href='my_orders.php';</script>";
exit;
?>

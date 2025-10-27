<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']); // sanitize input
    $customer_id = $_SESSION['customer_id'];

    // Ensure the cart item belongs to the logged-in customer before deleting
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $cart_id, $customer_id);

    if ($stmt->execute()) {
        // Redirect back to cart
        header("Location: cart.php?msg=deleted");
        exit;
    } else {
        echo "❌ Error deleting item.";
    }
} else {
    header("Location: cart.php");
    exit;
}
?>

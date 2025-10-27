<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['customer_id'];
    $product_id = $_POST['product_id'];
    $quantity   = $_POST['quantity'];


    $check = $conn->prepare("SELECT id, quantity FROM cart WHERE customer_id=? AND product_id=?");
    $check->bind_param("ii", $customer_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $update->bind_param("ii", $new_qty, $row['id']);
        $update->execute();
        $update->close();
    } else {

        $insert = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $customer_id, $product_id, $quantity);
        $insert->execute();
        $insert->close();
    }

    $check->close();
    header("Location: cart.php");
    exit;
}
?>

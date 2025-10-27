<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$result = $conn->query("
    SELECT c.id, p.name, p.price, p.image, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id=p.id 
    WHERE c.customer_id='$customer_id'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
   <style>
      
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Roboto, sans-serif;
}

body {
    background: linear-gradient(135deg, #FFF8E1, #FFF3CD);
    color: #333;
}

/* ====== Container ====== */
.container {
    width: 90%;
    max-width: 1100px;
    margin: 30px auto;
    padding: 25px;
    background: #fffbea;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.title {
    text-align: center;
    color: #D97706;
    margin-bottom: 25px;
    font-size: 1.8rem;
}

/* ====== Buttons ====== */
.btn-back, .btn-checkout {
    display: inline-block;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-back {
    background: #F59E0B;
    color: white;
}

.btn-back:hover {
    background: #D97706;
}

.btn-checkout {
    background: #CA8A04;
    color: white;
    float: right;
    margin-top: 10px;
}

.btn-checkout:hover {
    background: #A16207;
}

/* ====== Table ====== */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.styled-table th, .styled-table td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

.styled-table th {
    background: #FACC15;
    color: #6B4E00;
    font-weight: 600;
}

.styled-table tr:nth-child(even) {
    background: #FFFDF2;
}

.styled-table tr:hover {
    background: #FFF9C4;
}

/* ====== Product Image ====== */
.product-img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

/* ====== Delete Button ====== */
.btn-delete {
    background: #F87171;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: 0.3s;
    display: inline-block;
}

.btn-delete:hover {
    background: #DC2626;
}

/* ====== Total ====== */
.total-label {
    text-align: right;
    margin-top: 20px;
    font-size: 1.2rem;
    font-weight: 600;
    color: #D97706;
}

/* ====== Responsive ====== */
@media(max-width: 768px) {
    .btn-checkout {
        float: none;
        display: block;
        width: 100%;
        margin-top: 20px;
    }

    .total-label {
        text-align: center;
    }
}

   </style>
</head>
<body>
<div class="container">
    <h2 class="title">🛒 My Cart</h2>
    <a href="customer_dashboard.php" class="btn-back">⬅ Back to Products</a>

    <!-- Checkout Form -->
    <form method="POST" action="checkout.php" onsubmit="return validateCheckout();">
    <table class="styled-table">
        <tr>
            <th>Select</th>
            <th>Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php 
        while($row = $result->fetch_assoc()): 
            $line_total = $row['price'] * $row['quantity'];
        ?>
        <tr>
            <td>
                <input type="checkbox" name="selected[]" value="<?= $row['id']; ?>" 
                       class="product-check" data-price="<?= $line_total; ?>">
            </td>
            <td><img src="<?= $row['image']; ?>" class="product-img"></td>
            <td><?= $row['name']; ?></td>
            <td>₱<?= number_format($row['price'], 2); ?></td>
            <td><?= $row['quantity']; ?></td>
            <td>₱<?= number_format($line_total, 2); ?></td>
            <td>
                <!-- delete button as a link -->
                <a href="remove_from_cart.php?id=<?= $row['id']; ?>" 
                   class="btn-delete" 
                   onclick="return confirm('Are you sure you want to remove this item?');">
                   🗑 Delete
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3 class="total-label">Total: ₱<span id="total">0.00</span></h3>
    <button type="submit" class="btn-checkout">✅ Checkout</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const checkboxes = document.querySelectorAll(".product-check");
    const totalSpan = document.getElementById("total");

    function updateTotal() {
        let total = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.price);
            }
        });
        totalSpan.textContent = total.toFixed(2);
    }

    checkboxes.forEach(cb => cb.addEventListener("change", updateTotal));
});


function validateCheckout() {
    const checkboxes = document.querySelectorAll(".product-check:checked");
    if (checkboxes.length === 0) {
        alert("⚠️ Please select at least one product to checkout.");
        return false;
    }
    return true;
}
</script>
</body>
</html>

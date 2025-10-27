<?php
session_start();
include 'db.php';

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit;
}

$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <style>
        /* ====== Reset & Global ====== */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

        body {
            background: linear-gradient(135deg, #FFF8E1, #FFF3CD);
            color: #333;
            padding: 30px;
        }

        /* ====== Header ====== */
        h2 {
            color: #D97706;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        h3 {
            color: #B45309;
            margin: 20px 0 15px;
        }

        /* ====== Navigation Bar ====== */
        .top-nav {
            background: linear-gradient(180deg, #FFF9C4, #FFEB99);
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .top-nav a {
            text-decoration: none;
            color: #6B4E00;
            background: #FCD34D;
            padding: 10px 15px;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .top-nav a:hover {
            background: #FBBF24;
            transform: translateY(-2px);
        }

        .top-nav a:last-child {
            background: #F87171;
            color: #fff;
        }

        .top-nav a:last-child:hover {
            background: #DC2626;
        }

        /* ====== Product Grid ====== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }

        /* ====== Product Cards ====== */
        .product-card {
            background: #fffbea;
            border-radius: 15px;
            padding: 18px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .product-card img {
            max-width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .product-card h4 {
            color: #B45309;
            font-size: 1.2rem;
            margin-bottom: 6px;
        }

        .product-card p {
            font-size: 14px;
            color: #555;
            margin: 4px 0;
        }

        .price {
            font-weight: bold;
            color: #D97706;
            margin: 10px 0;
            font-size: 1rem;
        }

        /* ====== Add to Cart Form ====== */
        .product-card form {
            margin-top: 10px;
        }

        .product-card input[type="number"] {
            width: 60px;
            padding: 6px;
            border-radius: 8px;
            border: 1px solid #E5E7EB;
            margin-right: 8px;
            text-align: center;
        }

        .product-card button {
            background: #F59E0B;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s;
        }

        .product-card button:hover {
            background: #D97706;
            transform: translateY(-2px);
        }

        /* ====== Responsive ====== */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .top-nav {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['customer_name']; ?>!</h2>
    
    <div class="top-nav">
        <a href="cart.php">🛒 View Cart</a>
        <a href="my_orders.php">📦 My Orders</a>
        <a href="logout1.php">🚪 Logout</a>
    </div>

    <h3>Available Products</h3>
    <div class="product-grid">
        <?php while($row = $products->fetch_assoc()): ?>
            <div class="product-card">
                <?php if (!empty($row['image'])): ?>
                    <img src="<?= $row['image']; ?>" alt="<?= $row['name']; ?>">
                <?php else: ?>
                    <img src="default.png" alt="No Image">
                <?php endif; ?>
                <h4><?= $row['name']; ?></h4>
                <p><?= $row['description']; ?></p>
                <p class="price">₱<?= number_format($row['price'], 2); ?></p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

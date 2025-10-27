<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';

// --- Filters ---
$where = "WHERE o.status IN ('Received','Shipped')";
$filter_product = "";
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from = $_GET['from_date'];
    $to = $_GET['to_date'];
    $where .= " AND DATE(o.created_at) BETWEEN '$from' AND '$to'";
}
if (!empty($_GET['product'])) {
    $filter_product = $_GET['product'];
    $where .= " AND p.name LIKE '%$filter_product%'";
}

// --- Sales Query ---
$sales = $conn->query("
    SELECT 
        o.id AS order_id,
        c.fullname AS customer_name,
        p.name AS product_name,
        oi.quantity,
        oi.price,
        (oi.quantity * oi.price) AS subtotal,
        o.status,
        o.created_at
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    JOIN customers c ON o.customer_id = c.id
    $where
    ORDER BY o.created_at DESC
");

// --- Total Sales ---
$total_sales = $conn->query("
    SELECT SUM(oi.quantity * oi.price) AS total
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    $where
")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>ShopSphere - Sales</title>
    <style>
        /* ====== Reset & Global ====== */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: linear-gradient(135deg, #FFF8E1, #FFF3CD); color: #333; }

        /* ====== Sidebar ====== */
        .sidebar {
            position: fixed; top: 0; left: 0; width: 250px; height: 100%;
            background: linear-gradient(180deg, #FFF9C4, #FFEB99);
            color: #333; padding: 20px; display: flex; flex-direction: column;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 { font-size: 1.6rem; margin-bottom: 10px; color: #6B4E00; }
        .sidebar .welcome { font-size: 0.95rem; margin-bottom: 25px; font-style: italic; color: #8B6F00; }
        .sidebar ul { list-style: none; flex-grow: 1; padding: 0; }
        .sidebar ul li { margin: 12px 0; }
        .sidebar ul li a {
            text-decoration: none; color: #333; padding: 10px 15px;
            display: flex; align-items: center; justify-content: space-between;
            border-radius: 8px; transition: 0.3s;
        }
        .sidebar ul li a:hover { background: rgba(255, 255, 255, 0.5); }
        .sidebar ul li a.logout {
            background: #F87171; font-weight: 600; text-align: center; color: #fff;
        }
        .sidebar ul li a.logout:hover { background: #DC2626; }

        /* ====== Main Content ====== */
        .main-content { margin-left: 250px; padding: 30px; }
        .main-content h1 {
            font-size: 2rem; margin-bottom: 20px;
            color: #D97706;
        }

        /* ====== Filter Form ====== */
        .form-container {
            background: #fffbea; padding: 20px; margin: 20px 0;
            border-radius: 15px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
            margin-bottom: 15px; font-size: 1.3rem; color: #CA8A04;
        }
        .form-container input {
            padding: 10px; border: 1px solid #E5E7EB; border-radius: 10px;
            margin-right: 10px; transition: 0.3s;
        }
        .form-container input:focus {
            border-color: #FCD34D; box-shadow: 0 0 6px rgba(252, 211, 77, 0.6); outline: none;
        }
        .form-container button {
            background: #F59E0B; color: #fff; padding: 10px 15px; border: none;
            border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        .form-container button:hover { background: #D97706; }

        /* ====== Sales Table ====== */
        table {
            width: 100%; border-collapse: collapse; margin-top: 15px;
            background: #fff; border-radius: 12px; overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        th, td { padding: 14px; text-align: center; border-bottom: 1px solid #eee; }
        th {
            background: #FACC15; color: #6B4E00; font-weight: 600;
        }
        tr:nth-child(even) { background: #FFFDF2; }

        /* ====== Total Sales ====== */
        .total {
            margin-top: 20px; font-size: 1.2rem; font-weight: 600;
            color: #D97706; text-align: right;
        }

        /* ====== Responsive ====== */
        @media(max-width:768px){
            .sidebar {
                position: relative; width: 100%; height: auto;
                flex-direction: row; align-items: center; justify-content: space-between;
            }
            .sidebar ul { display: flex; flex-wrap: wrap; gap: 10px; }
            .main-content { margin-left: 0; margin-top: 20px; padding: 20px; }
            table th, table td { font-size: 0.85rem; padding: 10px; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>ShopSphere</h2>
        <ul>
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="products.php">📦 Products</a></li>
            <li><a href="sales.php" style="background: rgba(255, 255, 255, 0.5); font-weight:600;">💰 Sales</a></li>
            <li><a href="customers.php">👥 Customers</a></li>
            <li><a href="inventory.php">📊 Inventory</a></li>
            <li><a href="reports.php">📑 Reports</a></li>
            <li><a href="orders.php">🛒 Orders</a></li>
            <li><a href="logout.php" class="logout">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>💰 Sales Report</h1>

        <!-- Filters -->
        <div class="form-container">
            <h3>Filter Sales</h3>
            <form method="get">
                From: <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>">
                To: <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>">
                Product: <input type="text" name="product" placeholder="Search Product..." value="<?= htmlspecialchars($filter_product) ?>">
                <button type="submit">Apply</button>
            </form>
        </div>

        <!-- Sales Table -->
        <table>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php 
            $counter = 1;
            while ($row = $sales->fetch_assoc()): 
            ?>
            <tr>
                <td><?= $counter++; ?></td>
                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                <td><?= htmlspecialchars($row['product_name']); ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td>₱<?= number_format($row['subtotal'], 2) ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- Total Sales -->
        <div class="total">
            Total Sales: ₱<?= number_format($total_sales['total'] ?? 0, 2) ?>
        </div>
    </div>
</body>
</html>

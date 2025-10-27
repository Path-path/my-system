<?php
include 'db.php';


$query = "
    SELECT 
        IFNULL(SUM(total), 0) AS total_sales, 
        IFNULL(AVG(total), 0) AS avg_sale, 
        COUNT(*) AS total_transactions
    FROM orders
    WHERE status = 'Received'
";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_sales = floatval($row['total_sales']);
$avg_sale = floatval($row['avg_sale']);
$total_transactions = intval($row['total_transactions']);

function format_currency($amount) {
    return number_format($amount, 2);
}


$daily_sales = [];
$daily_result = $conn->query("
    SELECT DATE(created_at) as sale_day, SUM(total) AS total
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND status = 'Received'
    GROUP BY DATE(created_at)
    ORDER BY sale_day ASC
");
while ($row = $daily_result->fetch_assoc()) {
    $daily_sales[$row['sale_day']] = floatval($row['total']);
}


$monthly_sales = [];
$monthly_result = $conn->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS sale_month, SUM(total) AS total
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) AND status = 'Received'
    GROUP BY DATE_FORMAT(created_at,'%Y-%m')
    ORDER BY sale_month ASC
");
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_sales[$row['sale_month']] = floatval($row['total']);
}
?>
<!DOCTYPE html>
<html>
<head>
   <title>Reports</title>
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

.sidebar h2.brand {
    color: #6B4E00;
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 10px;
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
.content,
.main-content {
    margin-left: 250px;
    padding: 30px;
    min-height: 100vh;
    background: linear-gradient(135deg, #FFF8E1, #FFF3CD);
}

.content h1,
.main-content h1 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #D97706;
}

.content h2,
.main-content h2 {
    font-size: 1.5rem;
    margin: 25px 0 15px;
    color: #CA8A04;
}

/* ====== Cards ====== */
.card-container,
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.card {
    background: #fffbea;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

.card h2,
.card h3 {
    font-size: 1.4rem;
    margin-bottom: 10px;
    color: #D97706;
}

.card p {
    font-size: 1.2rem;
    font-weight: bold;
    color: #EAB308;
}

/* ====== Buttons & Links ====== */
button,
a.btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button {
    border: none;
    background: #F59E0B;
    color: #fff;
}

button:hover {
    background: #D97706;
}

a.btn {
    background: #FACC15;
    color: #6B4E00;
}

a.btn:hover {
    background: #EAB308;
}

/* ====== Tables ====== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

table th, table td {
    padding: 14px 18px;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 0.95rem;
}

table th {
    background: #FACC15;
    color: #6B4E00;
    font-weight: 600;
}

table tr:nth-child(even) {
    background: #FFFDF2;
}

table tr:hover {
    background: #FFFBEA;
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

    .sidebar ul li {
        margin: 0;
    }

    .content,
    .main-content {
        margin-left: 0;
        margin-top: 20px;
        padding: 20px;
    }

    table th, table td {
        font-size: 0.85rem;
        padding: 10px;
    }
}

   </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="content">
    <h1>Reports</h1>

    <div class="card-container">
        <div class="card">
            <h2>Total Sales</h2>
            <p>₱<?php echo format_currency($total_sales); ?></p>
        </div>
        <div class="card">
            <h2>Average Sale</h2>
            <p>₱<?php echo format_currency($avg_sale); ?></p>
        </div>
        <div class="card">
            <h2>Total Transactions</h2>
            <p><?php echo $total_transactions; ?></p>
        </div>
    </div>

    <!-- Daily Sales -->
    <h2>Sales Last 7 Days</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Total Sales (₱)</th>
        </tr>
        <?php
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $total = isset($daily_sales[$date]) ? $daily_sales[$date] : 0;
            echo "<tr><td>$date</td><td>".format_currency($total)."</td></tr>";
        }
        ?>
    </table>

    <!-- Monthly Sales -->
    <h2>Sales Last 6 Months</h2>
    <table>
        <tr>
            <th>Month</th>
            <th>Total Sales (₱)</th>
        </tr>
        <?php
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $total = isset($monthly_sales[$month]) ? $monthly_sales[$month] : 0;
            echo "<tr><td>$month</td><td>".format_currency($total)."</td></tr>";
        }
        ?>
    </table>

    <!-- Recent Transactions -->
    <h2>Recent Transactions</h2>
    <table>
        <tr>
            <th>#</th> 
            <th>Customer</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Total (₱)</th>
            <th>Date</th>
        </tr>
        <?php
        $sales_result = $conn->query("
            SELECT o.id AS order_id, c.fullname, p.name AS product_name, oi.quantity, o.total, o.created_at
            FROM orders o
            JOIN customers c ON o.customer_id = c.id
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE o.status = 'Received'
            ORDER BY o.created_at DESC
            LIMIT 20
        ");
        $counter = 1;
        while ($sale = $sales_result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $counter++; ?></td> <!-- Sequential numbering -->
            <td><?= htmlspecialchars($sale['fullname']); ?></td>
            <td><?= htmlspecialchars($sale['product_name']); ?></td>
            <td><?= $sale['quantity']; ?></td>
            <td><?= format_currency($sale['total']); ?></td>
            <td><?= $sale['created_at']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];


$notif_count = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE viewed=0")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>ShopSphere - Dashboard</title>
    <style>
  
* {
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
    font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

body {
    background: linear-gradient(135deg, #FFF8E1, #FFF3CD); /* light yellow gradient */
    color: #333;
}

/* ====== Sidebar ====== */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100%;
    background: linear-gradient(180deg, #FFF9C4, #FFEB99); /* friendly soft yellow gradient */
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
    padding: 0;
}

.sidebar ul li {
    margin: 12px 0;
}

.sidebar ul li a {
    text-decoration: none;
    color: #333;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar ul li a:hover {
    background: rgba(255, 255, 255, 0.5);
}

.sidebar ul li a.logout {
    background: #F87171; /* soft red for logout */
    font-weight: 600;
    text-align: center;
    color: #fff;
}

.sidebar ul li a.logout:hover {
    background: #DC2626;
}

/* ====== Notification Badge ====== */
.notif-badge {
    background: #FFD700;
    color: #333;
    border-radius: 50%;
    padding: 3px 8px;
    font-size: 0.8rem;
    font-weight: bold;
}

/* ====== Main Content ====== */
.main-content {
    margin-left: 250px;
    padding: 30px;
}

.main-content h1 {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #D97706;
}

.main-content p {
    font-size: 1rem;
    margin-bottom: 25px;
    color: #555;
}

/* ====== Cards ====== */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.card {
    background: #fffbea; /* soft creamy yellow */
    padding: 20px;
    border-radius: 15px;
    border: 1px solid #FCD34D;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.card h3 {
    font-size: 1.3rem;
    margin-bottom: 10px;
    color: #CA8A04;
}

.card p {
    font-size: 0.95rem;
    margin-bottom: 15px;
    color: #555;
}

.card a {
    text-decoration: none;
    font-weight: 600;
    color: #F59E0B;
    transition: 0.3s;
}

.card a:hover {
    color: #D97706;
}

/* ====== Responsive ====== */
@media (max-width:768px) {
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

    .main-content {
        margin-left: 0;
        margin-top: 20px;
        padding: 20px;
    }
}

    </style>
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
            <li>
                <a href="orders.php">🛒 Orders
                    <?php if($notif_count > 0){ ?>
                        <span class="notif-badge"><?= $notif_count ?></span>
                    <?php } ?>
                </a>
            </li>
            <li><a href="logout.php" class="logout">🚪 Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Dashboard</h1>
        <p>Select a module from the sidebar to get started.</p>

        <div class="cards">
            <div class="card">
                <h3>📦 Products</h3>
                <p>Manage product listings, categories, and stock.</p>
                <a href="products.php">Go to Products →</a>
            </div>
            <div class="card">
                <h3>💰 Sales</h3>
                <p>Track sales, view performance, and revenue reports.</p>
                <a href="sales.php">Go to Sales →</a>
            </div>
            <div class="card">
                <h3>👥 Customers</h3>
                <p>Manage customer details, interactions, and feedback.</p>
                <a href="customers.php">Go to Customers →</a>
            </div>
            <div class="card">
                <h3>📊 Inventory</h3>
                <p>Check stock levels and manage reorder points.</p>
                <a href="inventory.php">Go to Inventory →</a>
            </div>
            <div class="card">
                <h3>📑 Reports</h3>
                <p>Generate business reports and analytics.</p>
                <a href="reports.php">Go to Reports →</a>
            </div>
            <div class="card">
                <h3>🛒 Orders</h3>
                <p>Pending orders.</p>
                <a href="orders.php">Go to Orders →</a>
            </div>
        </div>
    </div>
</body>
</html>

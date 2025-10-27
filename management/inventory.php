<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';


$inventory = $conn->query("
    SELECT i.id, p.name, p.category, i.stock
    FROM inventory i
    JOIN products p ON i.product_id = p.id
    ORDER BY i.stock ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>SmartBiz Manager - Inventory</title>
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

/* ====== Sidebar (Friendly Yellow Theme) ====== */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100%;
    background: linear-gradient(180deg, #FFF9C4, #FFEB99); /* soft friendly yellow */
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

/*  Logout Button (Same as Dashboard) */
.sidebar ul li a.logout {
    background: #F87171; /* soft red for logout */
    font-weight: 600;
    text-align: center;
    color: #fff;
    border-radius: 8px;
    margin-top: auto;
    transition: 0.3s ease;
}

.sidebar ul li a.logout:hover {
    background: #DC2626; /* darker red hover */
}

/* ====== Main Content ====== */
.main-content {
    margin-left: 250px;
    padding: 30px;
}

.main-content h1 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #D97706;
}

.main-content h3 {
    margin-bottom: 10px;
    color: #B45309;
}

.main-content p {
    font-size: 1rem;
    margin-bottom: 25px;
    color: #555;
}

/* ====== Table ====== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background: #fffbea; /* soft creamy yellow */
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 14px 16px;
    text-align: left;
    font-size: 0.95rem;
    border-bottom: 1px solid #f0e6a8;
}

table th {
    background: #FCD34D;
    color: #6B4E00;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

table tr:hover {
    background: #fff8dc;
}

table td span {
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 6px;
}

table td span.low {
    background: #ffe5e5;
    color: #d9534f;
}

table td span.in {
    background: #e5f7e5;
    color: #28a745;
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

    .main-content {
        margin-left: 0;
        margin-top: 20px;
        padding: 20px;
    }
}
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1>📊 Inventory</h1>

        <h3>Stock Levels</h3>
        <table>
            <tr>
                <th>ID</th><th>Product</th><th>Category</th><th>Stock</th><th>Status</th>
            </tr>
            <?php while ($row = $inventory->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><?= $row['stock'] ?></td>
                <td>
                    <?php if ($row['stock'] <= 5) { ?>
                        <span class="low">Low Stock</span>
                    <?php } else { ?>
                        <span class="in">In Stock</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

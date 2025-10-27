<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';

// Add Customer
if (isset($_POST['add_customer'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $conn->query("INSERT INTO customers (name,email,phone,address) VALUES ('$name','$email','$phone','$address')");
}

$customers = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>SmartBiz Manager - Customers</title>
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

/* ===== Logout Button ===== */
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
.main-content {
    margin-left: 250px;
    padding: 30px;
}

.main-content h1 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #D97706;
}

/* ====== Table & Cards ====== */
.table-container, .card-container {
    background: #fffbea;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.table-container h3, .card-container h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: #CA8A04;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

th {
    background: #FACC15;
    color: #6B4E00;
    font-weight: 600;
}

tr:nth-child(even) {
    background: #FFFDF2;
}

/* ===== Buttons ===== */
button, .btn {
    background: #F59E0B;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button:hover, .btn:hover {
    background: #D97706;
}

/* ====== Responsive ====== */
@media(max-width:768px){
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
    <div class="main-content">
        <h1>👥 Customers</h1>

        <div class="form-container">
            <h3>Add Customer</h3>
            <form method="POST">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
              
                <textarea name="address" placeholder="Address"></textarea>
                <button type="submit" name="add_customer">Add Customer</button>
            </form>
        </div>

        <h3>Customer List</h3>
        <table>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>
            </tr>
            <?php while ($c = $customers->fetch_assoc()) { ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= $c['fullname'] ?></td>
                <td><?= $c['email'] ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

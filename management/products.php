<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';

// Handle Add Product
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Image Upload
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = "uploads/" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $sql = "INSERT INTO products (name, description, price, category, stock, image)
            VALUES ('$name','$description','$price','$category','$stock','$image')";
    if ($conn->query($sql)) {
        $product_id = $conn->insert_id;
        $conn->query("INSERT INTO inventory (product_id, stock) VALUES ('$product_id', '$stock')");
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM order_items WHERE product_id = $id");
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: products.php");
    exit;
}

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShopSphere - Products</title>
    <style>
    /* ====== Reset & Global ====== */
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
    .main-content {
        margin-left: 250px;
        padding: 30px;
    }

    .main-content h1 {
        font-size: 2rem;
        margin-bottom: 20px;
        color: #D97706;
    }

    /* ====== Form Container ====== */
    .form-container {
        background: #fffbea;
        padding: 25px;
        margin: 25px 0;
        border-radius: 15px;
        border: 1px solid #FCD34D;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .form-container h3 {
        margin-bottom: 15px;
        font-size: 1.3rem;
        color: #CA8A04;
    }

    .form-container form {
        display: grid;
        gap: 12px;
    }

    .form-container input,
    .form-container textarea,
    .form-container select {
        width: 100%;
        padding: 12px;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        font-size: 1rem;
        transition: 0.3s;
        background: #fff;
    }

    .form-container input:focus,
    .form-container textarea:focus,
    .form-container select:focus {
        border-color: #FBBF24;
        box-shadow: 0 0 6px rgba(251,191,36,0.4);
        outline: none;
    }

    .form-container button {
        background: linear-gradient(135deg, #FACC15, #FBBF24);
        color: #333;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        cursor: pointer;
        font-weight: 600;
        transition: 0.3s;
    }

    .form-container button:hover {
        background: linear-gradient(135deg, #FBBF24, #F59E0B);
        color: #fff;
    }

    /* ====== Product Table ====== */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: #fffbea;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #FCD34D;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    table th, table td {
        padding: 14px;
        text-align: center;
        border-bottom: 1px solid #f5eebc;
    }

    table th {
        background: #FDE68A;
        color: #6B4E00;
        font-weight: 600;
    }

    table tr:nth-child(even) {
        background: #FFFDF2;
    }

    table img {
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* ====== Action Button ====== */
    table a {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        color: #fff;
        background: #F87171;
        transition: 0.3s;
    }

    table a:hover {
        background: #DC2626;
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
        <h1>📦 Products</h1>

        <div class="form-container">
            <h3>Add New Product</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="text" name="category" placeholder="Category" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="number" name="stock" placeholder="Stock" required>
                <input type="file" name="image">
                <button type="submit" name="add">Add Product</button>
            </form>
        </div>

        <h3 style="color:#CA8A04;">Product List</h3>
        <table>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
            <?php 
            $counter = 1;
            while ($row = $products->fetch_assoc()) { ?>
            <tr>
                <td><?= $counter++; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['category']); ?></td>
                <td>₱<?= number_format($row['price'],2) ?></td>
                <td><?= $row['stock'] ?></td>
                <td><img src="<?= $row['image'] ?>" width="50"></td>
                <td><a href="products.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

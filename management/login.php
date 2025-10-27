<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        if (password_verify($password, $customer['password'])) {
            $_SESSION['customer_id']   = $customer['id'];
            $_SESSION['customer_name'] = $customer['name'];
            header("Location: customer_dashboard.php");
            exit;
        } else {
            $error = " Invalid password.";
        }
    } else {
        $error = " No account found with that username.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Login</title>
    <style>
/* ====== Reset ====== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* ====== Background (Same as other pages) ====== */
body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;

    /* Keep your background image */
    background: linear-gradient(rgba(255,255,255,0.15), rgba(255,255,255,0.15)),
                url('images/shopsphere-bg.png') no-repeat center center fixed;
    background-size: cover;
    background-attachment: fixed;
}

/* ====== Form Container (Glass Style) ====== */
.form-container {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 2rem 2.5rem;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
    animation: fadeIn 0.8s ease;
}

/* ====== Header ====== */
h2 {
    margin-bottom: 1.2rem;
    color: #FFB300; /* Changed from #1E3A84 to yellow */
    font-size: 1.9rem;
    font-weight: bold;
}

/* ====== Form ====== */
form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ====== Input Fields ====== */
input {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: 0.3s;
}

input:focus {
    border-color: #FFD54F; /* Soft friendly yellow */
    box-shadow: 0 0 6px rgba(255, 213, 79, 0.5);
    outline: none;
}

/* ====== Button ====== */
button {
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #FFD54F, #FFB300); /* Friendly yellow gradient */
    color: #1E3A84;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}

button:hover {
    background: linear-gradient(135deg, #FFEE58, #FBC02D);
    transform: translateY(-2px);
}

/* ====== Text & Links ====== */
p {
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #333;
}

a {
    color: #1E3A84; /* Deep blue for consistency */
    text-decoration: none;
    font-weight: bold;
    transition: 0.2s;
}

a:hover {
    color: #FFB300;
    text-decoration: underline;
}

/* ====== Status Messages ====== */
.error {
    background: #FFF3CD;
    color: #856404;
    padding: 10px;
    margin-bottom: 1rem;
    border: 1px solid #FFEEBA;
    border-radius: 8px;
    font-size: 0.95rem;
}

.success {
    background: #D4EDDA;
    color: #155724;
    padding: 10px;
    margin-bottom: 1rem;
    border: 1px solid #C3E6CB;
    border-radius: 8px;
    font-size: 0.95rem;
}

/* ====== Animation ====== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}
    </style>
</head>
<body>
<div class="form-container">
    <h2>ShopSphere</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($_GET['success'])) echo "<p class='success'>✅ Registration successful! Please login.</p>"; ?>

    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don’t have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>

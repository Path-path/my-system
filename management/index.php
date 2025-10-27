<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['username'] = $row['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Smart Bizz - Login</title>
  <style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

/* ====== Background ====== */
body.login-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(rgba(255,255,255,0.15), rgba(255,255,255,0.15)),
                url('images/shopsphere-bg.png') no-repeat center center fixed;
    background-size: cover;
    background-attachment: fixed;
}

/* ====== Login Container ====== */
.login-container {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    padding: 40px 35px;
    border-radius: 18px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    text-align: center;
    animation: fadeIn 0.6s ease-in-out;
}

/* ====== Header ====== */
.login-container h2 {
    margin-bottom: 25px;
    font-size: 1.9rem;
    font-weight: bold;
    color: #FFB300; /* Changed from #1E3A84 to yellow */
}

/* ====== Error Message ====== */
.error {
    background: #FFF3CD;
    color: #856404;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid #FFEEBA;
    border-radius: 8px;
    font-size: 0.95rem;
}

/* ====== Input Fields ====== */
form input {
    width: 100%;
    padding: 14px;
    margin: 12px 0;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 1rem;
    transition: 0.3s;
}

form input:focus {
    border-color: #FFD54F; /* Soft yellow accent */
    box-shadow: 0 0 6px rgba(255, 213, 79, 0.5);
    outline: none;
}

/* ====== Login Button ====== */
form button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #FFD54F, #FFB300); /* Friendly yellow gradient */
    color: #1E3A84;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

form button:hover {
    background: linear-gradient(135deg, #FFEE58, #FBC02D);
    transform: translateY(-2px);
}

/* ====== Animations ====== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ====== Responsive ====== */
@media (max-width: 500px) {
    .login-container {
        padding: 30px 20px;
    }

    .login-container h2 {
        font-size: 1.6rem;
    }
}
    </style>  
</head>
<body class="login-page">
    <div class="login-container">
        <h2>ShopSphere</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

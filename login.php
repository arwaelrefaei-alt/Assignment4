

<?php
require 'config.php';

// تسجيل الخروج إذا تم طلبه
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = validNum($_POST['customerNumber']);
    $pass = validStr($_POST['password'], 50);

    if ($id && $pass) {
        $sql = "SELECT * FROM customers WHERE customerNumber = $id AND password = '$pass'";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) == 1) {
            $user = mysqli_fetch_assoc($res);
            $_SESSION['user_id'] = $user['customerNumber'];
            $_SESSION['user_name'] = $user['customerName'];
            header("Location: index.php");
            exit();
        } else {
            $error = "رقم العميل أو الباسورد غير صحيح.";
        }
    } else {
        $error = "البيانات المدخلة غير صحيحة أو طويلة جداً.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        form { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #333; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <form method="POST">
        <h3>تسجيل الدخول</h3>
        <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="text" name="customerNumber" placeholder="رقم العميل (ID)" required>
        <input type="password" name="password" placeholder="الباسورد" required>
        <button type="submit">دخول</button>
    </form>
</body>
</html>
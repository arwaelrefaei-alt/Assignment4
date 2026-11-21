

<?php
require 'config.php';
checkAuth(); // ممنوع الدخول بدون Login

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>MySQL Task</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f9f9f9; }
        nav { background: #222; padding: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        nav a { color: white; text-decoration: none; margin: 5px 8px; font-size: 14px; }
        nav a:hover { color: #00d2ff; }
        .header { height: 85vh; background: url('https://unsplash.com') center/cover; display: flex; justify-content: center; align-items: center; color: white; text-shadow: 2px 2px 10px #000; font-size: 2.5rem; }
        .container { padding: 20px; max-width: 1000px; margin: auto; background: white; margin-top: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: right; }
        th { background: #333; color: white; }
        form { margin-bottom: 20px; }
        input, select, button { padding: 8px; margin-left: 5px; }
    </style>
</head>
<body>

<nav>
    <div><strong style="color:#00d2ff;">MySQL Task</strong></div>
    <div>
        <a href="index.php?page=home">الرئيسية</a>
        <a href="index.php?page=1">1- أكبر من 20000</a>
        <a href="index.php?page=2">2- بحث ID</a>
        <a href="index.php?page=3">3- عدد الأوردرات</a>
        <a href="index.php?page=4">4- الأكثر مبيعاً</a>
        <a href="index.php?page=5">5- الموظف والمدير</a>
        <a href="index.php?page=6">6- بحث بالاسم</a>
        <a href="index.php?page=7">7- المدن</a>
        <a href="index.php?page=8">8- كمية المنتجات</a>
        <a href="index.php?page=9">9- أغنى 3</a>
        <a href="index.php?page=10">10- تفاصيل منتج</a>
        <a href="login.php?logout=1" style="color:#ff6b6b;">خروج</a>
    </div>
</nav>

<?php if ($page == 'home'): ?>
    <div class="header">Welcome to MySQL</div>
<?php else: ?>
<div class="container">

    <?php
    // 1- العملاء اللي مرتبهم أكبر من 20,000
    if ($page == '1') {
        echo "<h2>1- العملاء ذوو الحد الائتماني/المرتب أكبر من 20,000</h2>";
        $res = mysqli_query($conn, "SELECT customerNumber, customerName, creditLimit FROM customers WHERE creditLimit > 20000");
        echo "<table><tr><th>ID</th><th>الاسم</th><th>المبلغ</th></tr>";
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr><td>{$row['customerNumber']}</td><td>{$row['customerName']}</td><td>{$row['creditLimit']}</td></tr>";
        }
        echo "</table>";
    }

    // 2- بيانات عميل بالـ ID
    elseif ($page == '2') {
        echo "<h2>2- البحث عن عميل برقم الـ ID</h2>";
        echo "<form method='POST'><input type='text' name='id' placeholder='أدخل ID العميل' required><button type='submit'>بحث</button></form>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = validNum($_POST['id']);
            if ($id) {
                $res = mysqli_query($conn, "SELECT * FROM customers WHERE customerNumber = $id");
                if ($row = mysqli_fetch_assoc($res)) {
                    echo "<table><tr><th>ID</th><th>الاسم</th><th>الهاتف</th><th>المدينة</th><th>الرصيد</th></tr>";
                    echo "<tr><td>{$row['customerNumber']}</td><td>{$row['customerName']}</td><td>{$row['phone']}</td><td>{$row['city']}</td><td>{$row['creditLimit']}</td></tr></table>";
                } else echo "<p>لا يوجد عميل بهذا الرقم.</p>";
            } else echo "<p style='color:red;'>أدخل رقماً صحيحاً.</p>";
        }
    }

    // 3- عدد الأوردرات لكل عميل
    elseif ($page == '3') {
        echo "<h2>3- عدد الأوردرات لكل عميل</h2>";
        $res = mysqli_query($conn, "SELECT c.customerName, COUNT(o.orderNumber) AS total FROM customers c LEFT JOIN orders o ON c.customerNumber = o.customerNumber GROUP BY c.customerNumber");
        echo "<table><tr><th>اسم العميل</th><th>عدد الأوردرات</th></tr>";
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr><td>{$row['customerName']}</td><td>{$row['total']}</td></tr>";
        }
        echo "</table>";
    }

    // 4- أكثر المنتجات مبيعاً والمكسب
    elseif ($page == '4') {
        echo "<h2>4- أكثر المنتجات مبيعاً والأرباح</h2>";
        $res = mysqli_query($conn, "SELECT p.productName, SUM(od.quantityOrdered) AS qty, SUM(od.quantityOrdered * od.priceEach) AS profit FROM products p JOIN orderdetails od ON p.productCode = od.productCode GROUP BY p.productCode ORDER BY qty DESC");
        echo "<table><tr><th>المنتج</th><th>القطع المباعة</th><th>إجمالي المكسب</th></tr>";
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr><td>{$row['productName']}</td><td>{$row['qty']}</td><td>{$row['profit']}</td></tr>";
        }
        echo "</table>";
    }

    // 5- كل موظف واسم مديره (Self Join)
    elseif ($page == '5') {
        echo "<h2>5- الموظف واسم مديره</h2>";
        $res = mysqli_query($conn, "SELECT CONCAT(e.firstName,' ',e.lastName) AS emp, CONCAT(m.firstName,' ',m.lastName) AS mgr FROM employees e LEFT JOIN employees m ON e.reportsTo = m.employeeNumber");
        echo "<table><tr><th>اسم الموظف</th><th>اسم المدير</th></tr>";
        while ($row = mysqli_fetch_assoc($res)) {
            $mgr = $row['mgr'] ? $row['mgr'] : 'لا يوجد (مدير عام)';
            echo "<tr><td>{$row['emp']}</td><td>$mgr</td></tr>";
        }
        echo "</table>";
    }

    // 6- بحث باسم العميل
    elseif ($page == '6') {
        echo "<h2>6- البحث باسم العميل</h2>";
        echo "<form method='POST'><input type='text' name='name' placeholder='اكتب اسم العميل' required><button type='submit'>بحث</button></form>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = validStr($_POST['name'], 50);
            if ($name) {
                $res = mysqli_query($conn, "SELECT customerNumber, customerName, city FROM customers WHERE customerName LIKE '%$name%'");
                echo "<table><tr><th>ID</th><th>الاسم</th><th>المدينة</th></tr>";
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr><td>{$row['customerNumber']}</td><td>{$row['customerName']}</td><td>{$row['city']}</td></tr>";
                }
                echo "</table>";
            } else echo "<p style='color:red;'>أدخل اسماً صحيحاً.</p>";
        }
    }

    // 7- اختيار المدينة وعرض العملاء مرتبين
    elseif ($page == '7') {
        echo "<h2>7- عرض عملاء المدينة مرتبين بالأسماء</h2>";
        $cities = mysqli_query($conn, "SELECT DISTINCT city FROM customers ORDER BY city ASC");
        echo "<form method='POST'><select name='city'>";
        while ($c = mysqli_fetch_assoc($cities)) {
            echo "<option value='{$c['city']}'>{$c['city']}</option>";
        }
        echo "</select><button type='submit'>عرض</button></form>";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $city = validStr($_POST['city'], 50);
            if ($city) {
                $res = mysqli_query($conn, "SELECT customerName, phone FROM customers WHERE city = '$city' ORDER BY customerName ASC");
                echo "<table><tr><th>اسم العميل</th><th>الهاتف</th></tr>";
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr><td>{$row['customerName']}</td><td>{$row['phone']}</td></tr>";
                }
                echo "</table>";
            }
        }
    }

    // 8- المنتجات اللي مجموع قطعها أكبر من رقم (100 لـ 5000)
    elseif ($page == '8') {
        echo "<h2>8- المنتجات التي تجاوزت مبيعاتها رقماً معيناً (من 100 إلى 5000)</h2>";
        echo "<form method='POST'><input type='number' name='num' placeholder='رقم بين 100 و 5000' required><button type='submit'>عرض</button></form>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $num = validNum($_POST['num'], 100, 5000);
            if ($num) {
                $res = mysqli_query($conn, "SELECT p.productName, SUM(od.quantityOrdered) AS total FROM products p JOIN orderdetails od ON p.productCode = od.productCode GROUP BY p.productCode HAVING total > $num");
                echo "<table><tr><th>اسم المنتج</th><th>مجموع القطع</th></tr>";
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr><td>{$row['productName']}</td><td>{$row['total']}</td></tr>";
                }
                echo "</table>";
            } else echo "<p style='color:red;'>يجب إدخال رقم صحيح بين 100 و 5000.</p>";
        }
    }

    // 9- أغنى 3 أشخاص في مدينة معينة
    elseif ($page == '9') {
        echo "<h2>9- أغنى 3 عملاء في المدينة</h2>";
        echo "<form method='POST'><input type='text' name='city' placeholder='اكتب اسم المدينة (مثلاً: NYC)' required><button type='submit'>بحث</button></form>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $city = validStr($_POST['city'], 50);
            if ($city) {
                $res = mysqli_query($conn, "SELECT customerName, creditLimit FROM customers WHERE city = '$city' ORDER BY creditLimit DESC LIMIT 3");
                echo "<table><tr><th>الاسم</th><th>الرصيد</th></tr>";
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr><td>{$row['customerName']}</td><td>{$row['creditLimit']}</td></tr>";
                }
                echo "</table>";
            } else echo "<p style='color:red;'>أدخل اسم مدينة صحيح.</p>";
        }
    }

    // 10- تفاصيل مبيعات منتج معين
    elseif ($page == '10') {
        echo "<h2>10- تفاصيل مبيعات منتج (أدخل كود المنتج مثل: S10_1678)</h2>";
        echo "<form method='POST'><input type='text' name='pid' placeholder='كود المنتج (Product ID)' required><button type='submit'>عرض التفاصيل</button></form>";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
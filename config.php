

<?php
session_start();

// 1. الاتصال بقاعدة البيانات
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "classicmodels";    

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("فشل الاتصال: " . mysqli_connect_error());
}

// 2. دالة حماية الصفحات (نقطة 11)
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// 3. دوال التحقق من البيانات Validation (نقطة 12)
function validStr($str, $max = 100) {
    $str = trim($str);
    if (empty($str) || strlen($str) > $max) return false;
    return mysqli_real_escape_string($GLOBALS['conn'], htmlspecialchars($str));
}

function validNum($num, $min = 0, $max = 1000000) {
    if (!is_numeric($num)) return false;
    $val = (int)$num;
    if ($val < $min || $val > $max) return false;
    return $val;
}
?> 
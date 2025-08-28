<?php
$servername = "localhost";  // hoặc IP hoặc tên host MySQL
$username = "root";          // username
$password = "";              // mật khẩu, để trống nếu chưa đặt
$dbname = "qlvt";            // tên database

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// echo "Kết nối thành công!";
?>

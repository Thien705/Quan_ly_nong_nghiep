<?php
$servername = "localhost";  // hoặc IP hoặc tên host MySQL
$username = "root";         // username mặc định thường là root
$password = "";             // mật khẩu, để trống nếu bạn chưa đặt
$dbname = "qlvt";       // tên database bạn muốn kết nối

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// echo "Kết nối thành công!";
?>

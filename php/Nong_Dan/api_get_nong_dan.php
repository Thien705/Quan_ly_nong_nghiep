<?php
// Cấu hình kết nối
$host = 'localhost';
$db   = 'qlvt';
$user = 'root';
$pass = ''; // Thêm mật khẩu nếu có

$conn = new mysqli($host, $user, $pass, $db);
header('Content-Type: application/json');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die(json_encode(["error" => "Kết nối thất bại: " . $conn->connect_error]));
}

// Truy vấn tất cả dữ liệu từ bảng caytrong
$sql = "SELECT * FROM nongdan";
$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);    

$conn->close();
?>

<?php
// Cấu hình kết nối
$host = 'localhost';
$db = 'qlvt';     // <-- Thay bằng tên CSDL thật
$user = 'root';
$pass = '';          // <-- Thêm mật khẩu nếu có

$conn = new mysqli($host, $user, $pass, $db);

// Thiết lập header JSON
header('Content-Type: application/json');

// Kiểm tra kết nối
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Kết nối thất bại: " . $conn->connect_error]);
    exit;
}

// Câu lệnh SQL: JOIN để lấy đầy đủ thông tin
$sql = "
SELECT 
    t.MaND,
    n.HoTen,
    t.MaTD,
    d.DiaChi AS DiaChiDat,
    t.MaC,
    c.TenC,
    t.NgayTrong,
    t.DienTich
FROM trong t
JOIN nongdan n ON t.MaND = n.MaND
JOIN thuadat d ON t.MaTD = d.MaTD
JOIN caytrong c ON t.MaC = c.MaC
";

$result = $conn->query($sql);

// Xử lý dữ liệu
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    echo json_encode([]);
}

$conn->close();
?>

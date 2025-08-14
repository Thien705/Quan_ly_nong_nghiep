<?php
// Cấu hình kết nối
$host = 'localhost';
$db   = 'qlvt'; // ← thay bằng tên CSDL thật
$user = 'root';
$pass = '';     // ← thêm mật khẩu nếu có

$conn = new mysqli($host, $user, $pass, $db);

// Thiết lập header JSON
header('Content-Type: application/json');

// Kiểm tra kết nối
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Kết nối thất bại: " . $conn->connect_error]);
    exit;
}

// Nhận từ khóa từ POST
$keyword = isset($_POST['keyword']) ? $conn->real_escape_string($_POST['keyword']) : "";

// Tạo câu lệnh SELECT
$sql = "SELECT * FROM trong";

if (!empty($keyword)) {
    $sql .= " WHERE MaND LIKE '%$keyword%' 
              OR MaTD LIKE '%$keyword%' 
              OR MaC LIKE '%$keyword%' 
              OR NgayTrong LIKE '%$keyword%' 
              OR DienTich LIKE '%$keyword%'";
}

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode(["success" => false, "message" => "Không tìm thấy vùng trồng phù hợp"]);
}

$conn->close();
?>

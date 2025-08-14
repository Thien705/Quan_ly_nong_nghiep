<?php
// Cấu hình kết nối cơ sở dữ liệu
$host = 'localhost';
$db   = 'qlvt';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Thiết lập kiểu dữ liệu trả về là JSON
header('Content-Type: application/json');

// Kiểm tra kết nối
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Kết nối thất bại: " . $conn->connect_error
    ]);
    exit;
}

// Kiểm tra dữ liệu đầu vào
if (!isset($_POST['MaC']) || empty(trim($_POST['MaC']))) {
    http_response_code(422);
    echo json_encode([
        "success" => false,
        "message" => "Thiếu mã cây trồng (MaC)"
    ]);
    exit;
}

$mac = trim($_POST['MaC']);

// Sử dụng Prepared Statement để tránh SQL Injection
$stmt = $conn->prepare("DELETE FROM caytrong WHERE MaC = ?");
$stmt->bind_param("s", $mac);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "status" => "success",
            "message" => "Xóa cây trồng thành công"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "status" => "not_found",
            "message" => "Không tìm thấy cây trồng để xóa"
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "status" => "error",
        "message" => "Lỗi khi xóa: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>

<?php
// Cấu hình kết nối cơ sở dữ liệu
$host = 'localhost';
$db   = 'qlvt';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Thiết lập kiểu dữ liệu trả về là JSON
header('Content-Type: application/json; charset=UTF-8');

// Kiểm tra kết nối
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Kết nối thất bại: " . $conn->connect_error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra dữ liệu đầu vào
if (!isset($_POST['MaND']) || empty(trim($_POST['MaND']))) {
    http_response_code(422);
    echo json_encode([
        "success" => false,
        "message" => "Thiếu mã nông dân (MaND)"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$mand = trim($_POST['MaND']);

// Sử dụng Prepared Statement để tránh SQL Injection
$stmt = $conn->prepare("DELETE FROM nongdan WHERE MaND = ?");
$stmt->bind_param("s", $mand);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "status" => "success",
            "message" => "Xóa nông dân thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "status" => "not_found",
            "message" => "Không tìm thấy nông dân để xóa"
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "status" => "error",
        "message" => "Lỗi khi xóa: " . $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>
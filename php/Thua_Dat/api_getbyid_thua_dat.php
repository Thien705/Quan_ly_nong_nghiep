<?php
// Cấu hình kết nối
$host = 'localhost';
$db   = 'qlvt';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
header('Content-Type: application/json; charset=utf-8');

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Kết nối thất bại: " . $conn->connect_error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Lấy MaTD từ GET hoặc POST
$MaTD = $_GET['MaTD'] ?? $_POST['MaTD'] ?? '';

// Nếu mã thửa đất trống → trả về code đặc biệt
if (empty(trim($MaTD))) {
    echo json_encode([
        "success" => false,
        "reason"  => "empty_id",
        "message" => "Thiếu tham số MaTD"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Chuẩn bị câu lệnh truy vấn
$stmt = $conn->prepare("SELECT * FROM thuadat WHERE MaTD = ?");
$stmt->bind_param("s", $MaTD);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "success" => true,
            "data" => $row
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "success" => false,
            "reason"  => "not_found",
            "message" => "Không tìm thấy thửa đất"
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "reason"  => "query_error",
        "message" => "Lỗi truy vấn: " . $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>

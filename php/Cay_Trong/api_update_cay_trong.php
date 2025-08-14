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
if (
    !isset($_POST['MaC']) || empty(trim($_POST['MaC'])) ||
    !isset($_POST['TenC']) || !isset($_POST['CachSuDung'])
) {
    http_response_code(422);
    echo json_encode([
        "success" => false,
        "message" => "Thiếu dữ liệu đầu vào (MaC, TenC, CachSuDung)"
    ]);
    exit;
}

$mac = trim($_POST['MaC']);
$tenc = trim($_POST['TenC']);
$cachsudung = trim($_POST['CachSuDung']);

// Câu truy vấn cập nhật
$stmt = $conn->prepare("UPDATE caytrong SET TenC = ?, CachSuDung = ? WHERE MaC = ?");
$stmt->bind_param("sss", $tenc, $cachsudung, $mac);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "status" => "success",
            "message" => "Cập nhật cây trồng thành công"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "status" => "not_found",
            "message" => "Không tìm thấy hoặc dữ liệu không thay đổi"
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "status" => "error",
        "message" => "Lỗi khi cập nhật: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>

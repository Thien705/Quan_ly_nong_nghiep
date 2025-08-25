<?php
// Bật báo lỗi chi tiết (giúp debug)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cấu hình kết nối CSDL
$host = 'localhost';
$db   = 'qlvt';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
header('Content-Type: application/json; charset=UTF-8');

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Kết nối thất bại: " . $conn->connect_error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra dữ liệu đầu vào
$MaTD = $_POST['MaTD'] ?? '';
$MaTD = trim($MaTD);

if (empty($MaTD)) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu mã thửa đất (MaTD)"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // ==== 1. Xoá dữ liệu liên quan trong bảng 'trong' trước ====
    $stmt1 = $conn->prepare("DELETE FROM trong WHERE MaTD = ?");
    $stmt1->bind_param("s", $MaTD);
    $stmt1->execute();
    $stmt1->close();

    // ==== 2. Sau đó xoá dữ liệu trong bảng 'thuadat' ====
    $stmt2 = $conn->prepare("DELETE FROM thuadat WHERE MaTD = ?");
    $stmt2->bind_param("s", $MaTD);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Xóa thửa đất thành công (bao gồm dữ liệu liên quan trong bảng trong)"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Không tìm thấy thửa đất để xóa"
        ], JSON_UNESCAPED_UNICODE);
    }

    $stmt2->close();

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi khi xóa: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();

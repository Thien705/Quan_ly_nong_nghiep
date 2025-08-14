<?php
// Bật thông báo lỗi chi tiết
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

// Lấy dữ liệu từ POST
$MaND   = $_POST['MaND']   ?? '';
$HoTen  = $_POST['HoTen']  ?? '';
$DiaChi = $_POST['DiaChi'] ?? '';

// Kiểm tra dữ liệu đầu vào
if (!empty($MaND) && !empty($HoTen) && !empty($DiaChi)) {
    try {
        // Chuẩn bị câu lệnh UPDATE
        $stmt = $conn->prepare("UPDATE nongdan SET HoTen = ?, DiaChi = ? WHERE MaND = ?");
        $stmt->bind_param("sss", $HoTen, $DiaChi, $MaND);

        // Thực thi
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Cập nhật thông tin nông dân thành công"
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Không tìm thấy hoặc không có thay đổi dữ liệu"
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Không thể cập nhật dữ liệu"
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Lỗi: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu đầu vào"
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>

<?php
// Bật thông báo lỗi chi tiết (giúp debug)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

// Lấy dữ liệu từ POST
$MaND   = $_POST['MaND']   ?? '';
$HoTen  = $_POST['HoTen']  ?? '';
$CuTru  = $_POST['CuTru']  ?? '';   // đổi từ DiaChi -> CuTru

// Kiểm tra dữ liệu không được rỗng
if (!empty($MaND) && !empty($HoTen) && !empty($CuTru)) {
    try {
        // Chuẩn bị câu lệnh
        $stmt = $conn->prepare("INSERT INTO nongdan (MaND, HoTen, CuTru) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $MaND, $HoTen, $CuTru);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Thêm nông dân thành công"
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Không thể thêm dữ liệu"
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

<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

$MaTD   = $_POST['MaTD']   ?? '';
$MaND   = $_POST['MaND']   ?? '';
$DiaChi = $_POST['DiaChi'] ?? '';

if (!empty($MaTD) && !empty($MaND) && !empty($DiaChi)) {
    try {
        // ==== 1. Kiểm tra nông dân tồn tại ====
        $checkStmt = $conn->prepare("SELECT MaND FROM nongdan WHERE MaND = ?");
        $checkStmt->bind_param("s", $MaND);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Nông dân không tồn tại, không thể thêm thửa đất"
            ], JSON_UNESCAPED_UNICODE);
            $checkStmt->close();
            $conn->close();
            exit;
        }
        $checkStmt->close();

        // ==== 2. Nếu tồn tại thì cho insert ====
        $stmt = $conn->prepare("INSERT INTO thuadat (MaTD, MaND, DiaChi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $MaTD, $MaND, $DiaChi);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Thêm thửa đất thành công"
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

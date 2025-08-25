<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

// Nhận dữ liệu từ POST
$MaTD   = $_POST['MaTD']   ?? '';
$TenTD  = $_POST['TenTD']  ?? '';
$MaND   = $_POST['MaND']   ?? '';
$DiaChi = $_POST['DiaChi'] ?? '';
$MaC    = $_POST['MaC']    ?? '';
$TGBD   = $_POST['ThoiGianBatDau']  ?? '';
$TGKT   = $_POST['ThoiGianKetThuc'] ?? '';

// Kiểm tra dữ liệu đầu vào
if (!empty($MaTD) && !empty($TenTD) && !empty($MaND) && !empty($DiaChi) && !empty($MaC) && !empty($TGBD) && !empty($TGKT)) {
    try {
        // ==== 1. Kiểm tra nông dân tồn tại ====
        $checkND = $conn->prepare("SELECT MaND FROM nongdan WHERE MaND = ?");
        $checkND->bind_param("s", $MaND);
        $checkND->execute();
        $rsND = $checkND->get_result();

        if ($rsND->num_rows === 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Nông dân không tồn tại"
            ], JSON_UNESCAPED_UNICODE);
            $checkND->close();
            $conn->close();
            exit;
        }
        $checkND->close();

        // ==== 2. Kiểm tra cây trồng tồn tại ====
        $checkC = $conn->prepare("SELECT MaC FROM caytrong WHERE MaC = ?");
        $checkC->bind_param("s", $MaC);
        $checkC->execute();
        $rsC = $checkC->get_result();

        if ($rsC->num_rows === 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Cây trồng không tồn tại"
            ], JSON_UNESCAPED_UNICODE);
            $checkC->close();
            $conn->close();
            exit;
        }
        $checkC->close();

        // ==== 3. Thêm dữ liệu vào bảng thuadat ====
        $stmt = $conn->prepare("INSERT INTO thuadat (MaTD, TenTD, MaND, DiaChi, MaC, ThoiGianBatDau, ThoiGianKetThuc) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $MaTD, $TenTD, $MaND, $DiaChi, $MaC, $TGBD, $TGKT);

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

<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

$MaTD = $_POST['MaTD'] ?? '';
$ThoiGianBatDau = $_POST['ThoiGianBatDau'] ?? '';
$ThoiGianKetThuc = $_POST['ThoiGianKetThuc'] ?? '';

if ($MaTD == '' || $ThoiGianBatDau == '' || $ThoiGianKetThuc == '') {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu khóa chính"]);
    exit;
}

$sql = "DELETE FROM trong WHERE MaTD=? AND ThoiGianBatDau=? AND ThoiGianKetThuc=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $MaTD, $ThoiGianBatDau, $ThoiGianKetThuc);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Xóa thành công"]);
} else {
    echo json_encode(["status" => "error", "message" => "Lỗi: " . $stmt->error]);
}

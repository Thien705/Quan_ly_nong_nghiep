<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

// Nhận dữ liệu khóa chính
$MaND = $_POST['MaND'] ?? '';
$MaTD = $_POST['MaTD'] ?? '';
$MaC  = $_POST['MaC']  ?? '';
$BatDau = $_POST['ThoiGianBatDau'] ?? '';
$KetThuc = $_POST['ThoiGianKetThuc'] ?? '';

if ($MaND == '' || $MaTD == '' || $MaC == '' || $BatDau == '' || $KetThuc == '') {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu đầu vào"]);
    exit;
}

// Xoá dữ liệu theo toàn bộ khóa chính
$sql = "DELETE FROM trong WHERE MaND = ? AND MaTD = ? AND MaC = ? AND ThoiGianBatDau = ? AND ThoiGianKetThuc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $MaND, $MaTD, $MaC, $BatDau, $KetThuc);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Xóa vụ trồng thành công"]);
    } else {
        echo json_encode(["success" => false, "message" => "Không tìm thấy bản ghi cần xóa"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi xóa dữ liệu"]);
}

$stmt->close();
$conn->close();

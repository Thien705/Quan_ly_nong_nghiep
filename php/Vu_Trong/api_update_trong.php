<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

// Nhận dữ liệu mới và cũ
$MaND   = $_POST['MaND']   ?? '';
$MaTD   = $_POST['MaTD']   ?? '';
$MaC    = $_POST['MaC']    ?? '';
$BatDau = $_POST['ThoiGianBatDau'] ?? '';
$KetThuc= $_POST['ThoiGianKetThuc'] ?? '';
$OldMaND   = $_POST['OldMaND']   ?? '';
$OldMaTD   = $_POST['OldMaTD']   ?? '';
$OldMaC    = $_POST['OldMaC']    ?? '';
$OldBatDau = $_POST['OldBatDau'] ?? '';
$OldKetThuc= $_POST['OldKetThuc']?? '';

if ($MaND == '' || $MaTD == '' || $MaC == '' || $BatDau == '' || $KetThuc == '' ||
    $OldMaND == '' || $OldMaTD == '' || $OldMaC == '' || $OldBatDau == '' || $OldKetThuc == '') {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào"]);
    exit;
}

// Kiểm tra trùng khoảng thời gian (cho phép chạm biên)
$sql_check = "SELECT * FROM trong 
              WHERE MaND = ? AND MaTD = ? AND MaC = ?
              AND NOT (ThoiGianKetThuc <= ? OR ThoiGianBatDau >= ?)
              AND NOT (MaND = ? AND MaTD = ? AND MaC = ? AND ThoiGianBatDau = ? AND ThoiGianKetThuc = ?)";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ssssssssss", 
    $MaND, $MaTD, $MaC, 
    $BatDau, $KetThuc, 
    $OldMaND, $OldMaTD, $OldMaC, $OldBatDau, $OldKetThuc
);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Khoảng thời gian bị trùng với vụ khác!"]);
    exit;
}

// Nếu hợp lệ thì update
$sql = "UPDATE trong 
        SET MaND = ?, MaTD = ?, MaC = ?, ThoiGianBatDau = ?, ThoiGianKetThuc = ?
        WHERE MaND = ? AND MaTD = ? AND MaC = ? AND ThoiGianBatDau = ? AND ThoiGianKetThuc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", 
    $MaND, $MaTD, $MaC, $BatDau, $KetThuc, 
    $OldMaND, $OldMaTD, $OldMaC, $OldBatDau, $OldKetThuc
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Cập nhật vụ trồng thành công"]);
} else {
    echo json_encode(["status" => "error", "message" => "Lỗi khi cập nhật dữ liệu"]);
}

$stmt->close();
$conn->close();
?>

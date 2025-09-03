<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

$MaTD   = $_POST['MaTD'] ?? '';
$MaND   = $_POST['MaND'] ?? '';
$DiaChi = $_POST['DiaChi'] ?? '';

// Kiểm tra thiếu dữ liệu
if ($MaTD == '' || $MaND == '' || $DiaChi == '') {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

// Kiểm tra mã nông dân có tồn tại không
$check_nd = $conn->prepare("SELECT MaND FROM nongdan WHERE MaND=?");
$check_nd->bind_param("s", $MaND);
$check_nd->execute();
$res_nd = $check_nd->get_result();
if ($res_nd->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Mã nông dân không tồn tại"]);
    exit;
}

// Kiểm tra mã thửa đất trùng
$check = $conn->prepare("SELECT MaTD FROM thuadat WHERE MaTD=?");
$check->bind_param("s", $MaTD);
$check->execute();
$res = $check->get_result();
if ($res->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Mã thửa đất đã có người sở hữu"]);
    exit;
}

// Thêm mới
$stmt = $conn->prepare("INSERT INTO thuadat (MaTD, MaND, DiaChi) VALUES (?,?,?)");
$stmt->bind_param("sss", $MaTD, $MaND, $DiaChi);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Thêm thửa đất thành công"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi thêm thửa đất"]);
}
?>

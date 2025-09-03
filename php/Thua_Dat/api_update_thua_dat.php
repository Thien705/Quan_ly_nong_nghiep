<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

// Nhận dữ liệu từ POST
$MaTD   = $_POST['MaTD'] ?? '';
$MaND   = $_POST['MaND'] ?? '';
$DiaChi = $_POST['DiaChi'] ?? '';

if ($MaTD == '' || $MaND == '' || $DiaChi == '') {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

// Kiểm tra thửa đất có tồn tại chưa
$check = $conn->prepare("SELECT MaTD FROM thuadat WHERE MaTD=?");
$check->bind_param("s", $MaTD);
$check->execute();
$res = $check->get_result();
if ($res->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Thửa đất không tồn tại"]);
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

// Cập nhật
$stmt = $conn->prepare("UPDATE thuadat SET MaND=?, DiaChi=? WHERE MaTD=?");
$stmt->bind_param("sss", $MaND, $DiaChi, $MaTD);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Cập nhật thửa đất thành công"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật thửa đất"]);
}
?>

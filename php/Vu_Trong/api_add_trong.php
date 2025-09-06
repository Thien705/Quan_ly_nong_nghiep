<?php
include 'config.php';
header('Content-Type: application/json; charset=utf-8');

$MaTD   = $_POST['MaTD']   ?? '';
$MaND   = $_POST['MaND']   ?? '';
$MaC    = $_POST['MaC']    ?? '';
$BatDau = $_POST['ThoiGianBatDau'] ?? '';
$KetThuc= $_POST['ThoiGianKetThuc'] ?? '';

if ($MaTD == '' || $MaND == '' || $MaC == '' || $BatDau == '' || $KetThuc == '') {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào"]);
    exit;
}

// Kiểm tra ngày kết thúc phải sau ngày bắt đầu
if (strtotime($KetThuc) <= strtotime($BatDau)) {
    echo json_encode(["status" => "error", "message" => "Ngày kết thúc phải sau ngày bắt đầu"]);
    exit;
}

// Kiểm tra thửa đất có thuộc về đúng nông dân không
$sql_check_owner = "SELECT MaND FROM thuadat WHERE MaTD = ? LIMIT 1";
$stmt_owner = $conn->prepare($sql_check_owner);
$stmt_owner->bind_param("s", $MaTD);
$stmt_owner->execute();
$result_owner = $stmt_owner->get_result();
if ($row_owner = $result_owner->fetch_assoc()) {
    if ($row_owner['MaND'] !== $MaND) {
        echo json_encode(["status" => "error", "message" => "Thửa đất không thuộc về nông dân này!"]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Không tìm thấy thửa đất!"]);
    exit;
}

    // Kiểm tra trùng khoảng thời gian trên cùng thửa đất
    $sql_check = "SELECT * FROM trong 
                WHERE MaTD = ? 
                AND NOT (ThoiGianKetThuc < ? OR ThoiGianBatDau > ?)";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("sss", $MaTD, $BatDau, $KetThuc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Khoảng thời gian bị trùng hoặc chạm biên với vụ khác!"]);
        exit;
    }

// Nếu hợp lệ thì thêm
$sql = "INSERT INTO trong (MaTD, MaND, MaC, ThoiGianBatDau, ThoiGianKetThuc)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $MaTD, $MaND, $MaC, $BatDau, $KetThuc);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Thêm vụ trồng thành công"]);
} else {
    echo json_encode(["status" => "error", "message" => "Lỗi khi thêm dữ liệu"]);
}

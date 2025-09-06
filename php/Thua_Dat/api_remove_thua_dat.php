<?php
// Cấu hình kết nối DB qlvt
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlvt";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Kết nối database thất bại: " . $conn->connect_error]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$MaTD = $_POST['MaTD'] ?? '';
if ($MaTD == '') {
    echo json_encode(["success" => false, "message" => "Thiếu mã thửa đất"]);
    exit;
}

// Chỉ xoá thửa đất từ bảng thua_dat theo MaTD
$sql = "DELETE FROM thuadat WHERE MaTD = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Lỗi prepare: " . $conn->error]);
    $conn->close();
    exit;
}
$stmt->bind_param("s", $MaTD);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response = ["success" => true, "message" => "Xoá thửa đất thành công"];
    } else {
        $response = ["success" => false, "message" => "Không tìm thấy thửa đất cần xoá"];
    }
} else {
    $response = ["success" => false, "message" => "Lỗi khi xoá thửa đất: " . $stmt->error];
}

$stmt->close();
$conn->close();

echo json_encode($response);
exit;
?>

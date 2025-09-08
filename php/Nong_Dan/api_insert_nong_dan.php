<?php
// Bật thông báo lỗi chi tiết (giúp debug)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

// Cấu hình kết nối DB qlvt trực tiếp
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlvt";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["success" => false, "message" => "Kết nối database thất bại: " . $conn->connect_error]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Lấy dữ liệu từ POST
$MaND = $_POST['MaND'] ?? '';
$HoTen = $_POST['HoTen'] ?? '';
$CuTru = $_POST['CuTru'] ?? '';   
$NgaySinh = $_POST['NgaySinh'] ?? '';

// Kiểm tra dữ liệu không được rỗng
if ($MaND == '' || $HoTen == '' || $CuTru == '' || $NgaySinh == '') {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin: Mã nông dân, họ tên, cư trú, ngày sinh"]);
    $conn->close();
    exit;
}

// QUAN TRỌNG: Kiểm tra mã nông dân đã tồn tại chưa
$sql_check = "SELECT MaND FROM nongdan WHERE MaND = ?";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    echo json_encode(["success" => false, "message" => "Lỗi prepare statement: " . $conn->error]);
    $conn->close();
    exit;
}

$stmt_check->bind_param("s", $MaND);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Mã nông dân '$MaND' đã tồn tại. Vui lòng sử dụng mã khác!"]);
    $stmt_check->close();
    $conn->close();
    exit;
}

$stmt_check->close();

// Thêm nông dân mới
$sql_insert = "INSERT INTO nongdan (MaND, HoTen, CuTru, NgaySinh) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);

if (!$stmt_insert) {
    echo json_encode(["success" => false, "message" => "Lỗi prepare statement: " . $conn->error]);
    $conn->close();
    exit;
}

$stmt_insert->bind_param("ssss", $MaND, $HoTen, $CuTru, $NgaySinh);

if ($stmt_insert->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Thêm nông dân thành công",
        "data" => [
            "MaND" => $MaND,
            "HoTen" => $HoTen,
            "CuTru" => $CuTru,
            "NgaySinh" => $NgaySinh
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi thêm nông dân: " . $stmt_insert->error]);
}

$stmt_insert->close();
$conn->close();
?>

<?php
// Cấu hình kết nối
$host = 'localhost';
$db   = 'qlvt'; // Tên cơ sở dữ liệu
$user = 'root';
$pass = ''; // Mật khẩu nếu có

$conn = new mysqli($host, $user, $pass, $db);
header('Content-Type: application/json');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die(json_encode(["error" => "Kết nối thất bại: " . $conn->connect_error]));
}

// Câu lệnh SQL JOIN tất cả các bảng
$sql = "
SELECT 
    nd.MaND,
    nd.HoTen,
    nd.DiaChi AS DiaChiND,
    td.MaTD,
    td.DiaChi AS DiaChiTD,
    ct.MaC,
    ct.TenC,
    ct.CachSuDung,
    t.NgayTrong,
    t.DienTich
FROM trong t
JOIN nongdan nd ON t.MaND = nd.MaND
JOIN thuadat td ON t.MaTD = td.MaTD
JOIN caytrong ct ON t.MaC = ct.MaC
";

$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);

$conn->close();
?>
